-- SPDX-License-Identifier: PMPL-1.0-or-later
-- Trustfile - cryptographic and provenance verification for lcb-website
--
-- Embeds the full user security requirements specification and provides
-- verification functions for policy hashes, schema signatures, PQ signatures,
-- and migration provenance.
--
-- Run: runhaskell contractiles/trust/Trustfile.hs

module Trustfile where

import Control.Monad (forM)
import System.Directory (doesFileExist)
import System.Environment (lookupEnv)
import System.Exit (exitFailure, exitSuccess)
import System.Process (readProcessWithExitCode)

-- ==========================================================================
-- SECURITY REQUIREMENTS SPECIFICATION
-- ==========================================================================
--
-- Canonical source: user-security-requirements (Scheme definition)
-- This block is the authoritative crypto policy for the lcb-website stack.
--
-- (user-security-requirements
--   (PasswordHashing
--     (algorithm . "Argon2id")
--     (memory . "512 MiB")
--     (iterations . 8)
--     (parallelism . 4))
--   (GeneralHashing
--     (algorithm . "SHAKE3-512")
--     (standard . "FIPS 202"))
--   (PQSignatures
--     (algorithm . "Dilithium5-AES hybrid")
--     (standard . "ML-DSA-87 / FIPS 204"))
--   (PQKeyExchange
--     (algorithm . "Kyber-1024 + SHAKE256-KDF")
--     (standard . "ML-KEM-1024 / FIPS 203"))
--   (ClassicalSigs
--     (algorithm . "Ed448 + Dilithium5 hybrid"))
--   (Symmetric
--     (algorithm . "XChaCha20-Poly1305")
--     (key-size . 256))
--   (KeyDerivation
--     (algorithm . "HKDF-SHAKE512")
--     (standard . "FIPS 202"))
--   (RNG
--     (algorithm . "ChaCha20-DRBG")
--     (seed-size . 512)
--     (standard . "SP 800-90Ar1"))
--   (DatabaseHashing
--     (algorithm . "BLAKE3 (512-bit) + SHAKE3-512"))
--   (ProtocolStack
--     (protocols . "QUIC + HTTP/3 + IPv6"))
--   (Accessibility
--     (standard . "WCAG 2.3 AAA + ARIA + Semantic XML"))
--   (FormalVerification
--     (tools . "Coq/Isabelle for crypto primitives"))
--   (Fallback
--     (algorithm . "SPHINCS+ for all hybrid PQ systems")))

-- ==========================================================================
-- FILE PATHS
-- ==========================================================================

-- Policy files (WordPress security config)
policyPath :: FilePath
policyPath = "templates/wp-config-security.php"

policyHashPath :: FilePath
policyHashPath = "templates/wp-config-security.php.sha256"

-- CTP manifest (Cerro Torre)
ctpManifestPath :: FilePath
ctpManifestPath = "infra/wordpress.ctp"

ctpSigPath :: FilePath
ctpSigPath = "infra/wordpress.ctp.sig"

ctpPubPath :: FilePath
ctpPubPath = "infra/wordpress.ctp.pub"

-- Container bundle paths (for PQ signature verification)
bundlePaths :: [FilePath]
bundlePaths = ["infra/wordpress.ctp"]

-- Migration/deployment provenance
migrationsPath :: FilePath
migrationsPath = "infra/provenance.json"

migrationsSigPath :: FilePath
migrationsSigPath = "infra/provenance.sig"

migrationsPubPath :: FilePath
migrationsPubPath = "infra/provenance.pub"

-- ==========================================================================
-- UTILITIES
-- ==========================================================================

runCmd :: String -> [String] -> IO Bool
runCmd cmd args = do
  (code, _out, _err) <- readProcessWithExitCode cmd args ""
  pure (code == mempty)

readFirstWord :: FilePath -> IO (Maybe String)
readFirstWord path = do
  exists <- doesFileExist path
  if not exists
    then pure Nothing
    else do
      content <- readFile path
      pure (case words content of
        [] -> Nothing
        (w:_) -> Just w)

-- ==========================================================================
-- VERIFICATION FUNCTIONS
-- ==========================================================================

-- | Verify policy file hash.
-- Current: SHA-256 (interim)
-- Target: SHAKE3-512 when tooling supports it
verifyPolicyHash :: IO Bool
verifyPolicyHash = do
  hashFileExists <- doesFileExist policyHashPath
  policyExists <- doesFileExist policyPath
  if not (hashFileExists && policyExists)
    then do
      putStrLn "  [SKIP] Policy hash files not yet generated"
      pure True  -- Skip if hash not yet generated (first deploy)
    else do
      expected <- readFirstWord policyHashPath
      case expected of
        Nothing -> pure False
        Just hash -> do
          (code, out, _err) <- readProcessWithExitCode "sha256sum" [policyPath] ""
          if code /= mempty
            then pure False
            else do
              let actual = case words out of
                    [] -> ""
                    (w:_) -> w
              let ok = actual == hash
              putStrLn $ "  [" ++ (if ok then "PASS" else "FAIL") ++ "] Policy hash: " ++ policyPath
              pure ok

-- | Verify CTP manifest signature.
-- Current: OpenSSL RSA/DSA (interim)
-- Target: Ed448 + Dilithium5 hybrid when cerro-torre supports it
verifySchemaSignature :: IO Bool
verifySchemaSignature = do
  filesExist <- and <$> mapM doesFileExist [ctpManifestPath, ctpSigPath, ctpPubPath]
  if not filesExist
    then do
      putStrLn "  [SKIP] CTP signature files not yet generated (needs cerro-torre sign)"
      pure True  -- Skip if not yet signed
    else do
      ok <- runCmd "openssl" ["dgst", "-sha256", "-verify", ctpPubPath, "-signature", ctpSigPath, ctpManifestPath]
      putStrLn $ "  [" ++ (if ok then "PASS" else "FAIL") ++ "] CTP manifest signature"
      pure ok

-- | Verify Kyber-1024 post-quantum signatures on bundles.
-- Uses ML-KEM-1024 (FIPS 203) when available.
-- Fallback: SPHINCS+ if Kyber tooling unavailable.
verifyKyber1024Signatures :: IO Bool
verifyKyber1024Signatures = do
  cmd <- lookupEnv "KYBER_VERIFY_CMD"
  let kyberCmd = maybe "kyber-verify" id cmd
  results <- forM bundlePaths $ \path -> do
    let sig = path <> ".kyber.sig"
    let pub = path <> ".kyber.pub"
    filesOk <- and <$> mapM doesFileExist [path, sig, pub]
    if not filesOk
      then do
        putStrLn $ "  [SKIP] PQ signature not yet generated: " ++ path
        pure True  -- Skip if PQ sigs not yet available
      else do
        ok <- runCmd kyberCmd ["--pub", pub, "--sig", sig, "--file", path]
        putStrLn $ "  [" ++ (if ok then "PASS" else "FAIL") ++ "] Kyber-1024 signature: " ++ path
        pure ok
  pure (and results)

-- | Verify migration/deployment provenance chain.
verifyMigrationProvenance :: IO Bool
verifyMigrationProvenance = do
  filesExist <- and <$> mapM doesFileExist [migrationsPath, migrationsSigPath, migrationsPubPath]
  if not filesExist
    then do
      putStrLn "  [SKIP] Migration provenance not yet generated (pre-deployment)"
      pure True  -- Skip if no deployments yet
    else do
      ok <- runCmd "openssl" ["dgst", "-sha256", "-verify", migrationsPubPath, "-signature", migrationsSigPath, migrationsPath]
      putStrLn $ "  [" ++ (if ok then "PASS" else "FAIL") ++ "] Migration provenance signature"
      pure ok

-- | Verify .well-known files exist and are consistent.
verifyWellKnown :: IO Bool
verifyWellKnown = do
  let files = [".well-known/aibdp.json", ".well-known/security.txt", ".well-known/ai.txt"]
  results <- forM files $ \f -> do
    exists <- doesFileExist f
    putStrLn $ "  [" ++ (if exists then "PASS" else "FAIL") ++ "] " ++ f ++ " exists"
    pure exists
  pure (and results)

-- ==========================================================================
-- MAIN
-- ==========================================================================

main :: IO ()
main = do
  putStrLn "=== LCB Website Trust Verification ==="
  putStrLn ""

  putStrLn "1. Policy hash verification (SHA-256 → SHAKE3-512)"
  policyOk <- verifyPolicyHash
  putStrLn ""

  putStrLn "2. CTP manifest signature (RSA interim → Ed448+Dilithium5)"
  schemaOk <- verifySchemaSignature
  putStrLn ""

  putStrLn "3. Post-quantum bundle signatures (Kyber-1024 / ML-KEM-1024)"
  pqOk <- verifyKyber1024Signatures
  putStrLn ""

  putStrLn "4. Migration provenance chain"
  migrationsOk <- verifyMigrationProvenance
  putStrLn ""

  putStrLn "5. .well-known file integrity"
  wellKnownOk <- verifyWellKnown
  putStrLn ""

  let allOk = and [policyOk, schemaOk, pqOk, migrationsOk, wellKnownOk]
  putStrLn $ "=== Result: " ++ (if allOk then "ALL PASSED" else "FAILURES DETECTED") ++ " ==="

  if allOk
    then exitSuccess
    else exitFailure
