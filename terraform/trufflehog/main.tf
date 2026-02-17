terraform {
  required_version = ">= 1.5"
  required_providers {
    null = {
      source  = "hashicorp/null"
      version = ">= 3.2.0"
    }
  }
}

provider "null" {}

data "external" "git_head" {
  program = ["bash", "${path.root}/scripts/git-rev.sh"]
}

resource "null_resource" "trufflehog_scan" {
  provisioner "local-exec" {
    command = "${path.root}/scripts/run-trufflehog.sh"
    environment = {
      TF_FORCE_COLOR = "true"
    }
  }

  triggers = {
    allowlist = filebase64sha256("${path.root}/.trufflehog/allowlist.json")
    git_head  = data.external.git_head.result.head
  }
}
