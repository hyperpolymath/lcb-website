/* SPDX-License-Identifier: GPL-2.0-or-later */
/**
 * Accessibility Toolbar — Dark/Light/High-Contrast mode + Font scaling
 *
 * Reads preferences from localStorage.
 * Works alongside dark-mode.js (shares the sinople_theme storage key).
 *
 * @package Sinople
 * @since 2.1.0
 */

(function () {
  'use strict';

  var KEYS = {
    theme: 'sinople_theme',
    contrast: 'sinople_contrast',
    fontscale: 'sinople_fontscale'
  };

  var FONT_SCALES = ['small', 'normal', 'large', 'x-large'];
  var FONT_LABELS = ['S', 'M', 'L', 'XL'];

  var html = document.documentElement;

  // ----------------------------------------------------------------
  // Storage helpers
  // ----------------------------------------------------------------

  function store(key, value) {
    try { localStorage.setItem(key, value); } catch (e) { /* private browsing */ }
  }

  function retrieve(key) {
    try { return localStorage.getItem(key); } catch (e) { return null; }
  }

  // ----------------------------------------------------------------
  // Theme mode (light/dark)
  // ----------------------------------------------------------------

  function getTheme() {
    return html.getAttribute('data-theme') || 'light';
  }

  function setTheme(mode) {
    html.setAttribute('data-theme', mode);
    store(KEYS.theme, mode);
    updateModeButtons();
    updateDarkModeToggles(mode);
  }

  /** Keep existing header dark-mode-toggle buttons in sync. */
  function updateDarkModeToggles(theme) {
    var buttons = document.querySelectorAll('.dark-mode-toggle');
    var label = theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode';
    for (var i = 0; i < buttons.length; i++) {
      buttons[i].setAttribute('aria-label', label);
    }
  }

  // ----------------------------------------------------------------
  // High contrast
  // ----------------------------------------------------------------

  function getContrast() {
    return html.getAttribute('data-contrast') || 'normal';
  }

  function setContrast(mode) {
    html.setAttribute('data-contrast', mode);
    store(KEYS.contrast, mode);

    // Keep legacy body class for any existing CSS
    if (mode === 'high') {
      document.body.classList.add('high-contrast');
    } else {
      document.body.classList.remove('high-contrast');
    }

    updateModeButtons();
  }

  // ----------------------------------------------------------------
  // Font scaling
  // ----------------------------------------------------------------

  function getFontScale() {
    return html.getAttribute('data-fontscale') || 'normal';
  }

  function setFontScale(scale) {
    if (FONT_SCALES.indexOf(scale) === -1) return;
    html.setAttribute('data-fontscale', scale);
    store(KEYS.fontscale, scale);
    updateFontLabel();
  }

  function stepFontScale(direction) {
    var current = getFontScale();
    var idx = FONT_SCALES.indexOf(current);
    if (idx === -1) idx = 1; // default to "normal"
    var next = idx + direction;
    if (next < 0 || next >= FONT_SCALES.length) return;
    setFontScale(FONT_SCALES[next]);
  }

  // ----------------------------------------------------------------
  // UI Updates
  // ----------------------------------------------------------------

  function updateModeButtons() {
    var theme = getTheme();
    var contrast = getContrast();

    var lightBtn = document.getElementById('a11y-mode-light');
    var darkBtn = document.getElementById('a11y-mode-dark');
    var hcBtn = document.getElementById('a11y-mode-hc');

    if (lightBtn) lightBtn.setAttribute('aria-pressed', theme === 'light' && contrast !== 'high' ? 'true' : 'false');
    if (darkBtn) darkBtn.setAttribute('aria-pressed', theme === 'dark' && contrast !== 'high' ? 'true' : 'false');
    if (hcBtn) hcBtn.setAttribute('aria-pressed', contrast === 'high' ? 'true' : 'false');
  }

  function updateFontLabel() {
    var label = document.getElementById('a11y-fontsize-label');
    if (!label) return;
    var current = getFontScale();
    var idx = FONT_SCALES.indexOf(current);
    if (idx === -1) idx = 1;
    label.textContent = FONT_LABELS[idx];
  }

  // ----------------------------------------------------------------
  // Panel toggle
  // ----------------------------------------------------------------

  function togglePanel(fab, panel) {
    var isOpen = fab.getAttribute('aria-expanded') === 'true';
    fab.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
    panel.setAttribute('aria-hidden', isOpen ? 'true' : 'false');

    if (!isOpen) {
      // Focus the first interactive element in the panel
      var firstBtn = panel.querySelector('button');
      if (firstBtn) firstBtn.focus();
    }
  }

  // ----------------------------------------------------------------
  // Reset all
  // ----------------------------------------------------------------

  function resetAll() {
    setTheme('light');
    setContrast('normal');
    setFontScale('normal');
  }

  // ----------------------------------------------------------------
  // Initialise from stored preferences
  // ----------------------------------------------------------------

  function applyStoredPreferences() {
    var storedContrast = retrieve(KEYS.contrast);
    if (storedContrast === 'high') {
      html.setAttribute('data-contrast', 'high');
      document.body.classList.add('high-contrast');
    }

    var storedFont = retrieve(KEYS.fontscale);
    if (storedFont && FONT_SCALES.indexOf(storedFont) !== -1) {
      html.setAttribute('data-fontscale', storedFont);
    }
    // Theme is already applied by the inline <script> in variants.php
  }

  // ----------------------------------------------------------------
  // Build the toolbar DOM
  // ----------------------------------------------------------------

  function buildToolbar() {
    // FAB button
    var fab = document.createElement('button');
    fab.className = 'a11y-fab';
    fab.type = 'button';
    fab.id = 'a11y-fab';
    fab.setAttribute('aria-expanded', 'false');
    fab.setAttribute('aria-controls', 'a11y-toolbar');
    fab.setAttribute('aria-label', 'Accessibility settings');
    fab.innerHTML = '<i class="fa-solid fa-universal-access a11y-fab-icon-open" aria-hidden="true"></i>' +
                    '<i class="fa-solid fa-xmark a11y-fab-icon-close" aria-hidden="true"></i>';

    // Panel
    var panel = document.createElement('div');
    panel.className = 'a11y-toolbar';
    panel.id = 'a11y-toolbar';
    panel.setAttribute('role', 'region');
    panel.setAttribute('aria-label', 'Accessibility settings');
    panel.setAttribute('aria-hidden', 'true');

    panel.innerHTML =
      '<h2 class="a11y-toolbar-title">Accessibility</h2>' +

      // Display mode section
      '<div class="a11y-section">' +
        '<span class="a11y-section-label">Display Mode</span>' +
        '<div class="a11y-mode-group">' +
          '<button type="button" class="a11y-mode-btn" id="a11y-mode-light" aria-pressed="false">' +
            '<i class="fa-solid fa-sun" aria-hidden="true"></i>' +
            '<span>Light</span>' +
          '</button>' +
          '<button type="button" class="a11y-mode-btn" id="a11y-mode-dark" aria-pressed="false">' +
            '<i class="fa-solid fa-moon" aria-hidden="true"></i>' +
            '<span>Dark</span>' +
          '</button>' +
          '<button type="button" class="a11y-mode-btn" id="a11y-mode-hc" aria-pressed="false">' +
            '<i class="fa-solid fa-circle-half-stroke" aria-hidden="true"></i>' +
            '<span>Contrast</span>' +
          '</button>' +
        '</div>' +
      '</div>' +

      // Font size section
      '<div class="a11y-section">' +
        '<span class="a11y-section-label">Font Size</span>' +
        '<div class="a11y-fontsize-group">' +
          '<button type="button" class="a11y-fontsize-btn" id="a11y-font-decrease" aria-label="Decrease font size">' +
            '<span aria-hidden="true">A&minus;</span>' +
          '</button>' +
          '<span class="a11y-fontsize-label" id="a11y-fontsize-label" aria-live="polite">M</span>' +
          '<button type="button" class="a11y-fontsize-btn" id="a11y-font-increase" aria-label="Increase font size">' +
            '<span aria-hidden="true">A+</span>' +
          '</button>' +
        '</div>' +
      '</div>' +

      // Reset
      '<div class="a11y-section">' +
        '<button type="button" class="a11y-reset" id="a11y-reset">' +
          '<i class="fa-solid fa-rotate-left" aria-hidden="true"></i> Reset All' +
        '</button>' +
      '</div>';

    document.body.appendChild(panel);
    document.body.appendChild(fab);

    return { fab: fab, panel: panel };
  }

  // ----------------------------------------------------------------
  // Event binding
  // ----------------------------------------------------------------

  function bindEvents(fab, panel) {
    // Toggle panel
    fab.addEventListener('click', function (e) {
      e.preventDefault();
      togglePanel(fab, panel);
    });

    // Close panel on Escape
    panel.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        fab.setAttribute('aria-expanded', 'false');
        panel.setAttribute('aria-hidden', 'true');
        fab.focus();
      }
    });

    // Close panel when clicking outside
    document.addEventListener('click', function (e) {
      if (fab.getAttribute('aria-expanded') !== 'true') return;
      if (fab.contains(e.target) || panel.contains(e.target)) return;
      fab.setAttribute('aria-expanded', 'false');
      panel.setAttribute('aria-hidden', 'true');
    });

    // Mode buttons
    document.getElementById('a11y-mode-light').addEventListener('click', function () {
      setTheme('light');
      setContrast('normal');
    });

    document.getElementById('a11y-mode-dark').addEventListener('click', function () {
      setTheme('dark');
      setContrast('normal');
    });

    document.getElementById('a11y-mode-hc').addEventListener('click', function () {
      var isHC = getContrast() === 'high';
      setContrast(isHC ? 'normal' : 'high');
    });

    // Font size buttons
    document.getElementById('a11y-font-decrease').addEventListener('click', function () {
      stepFontScale(-1);
    });

    document.getElementById('a11y-font-increase').addEventListener('click', function () {
      stepFontScale(1);
    });

    // Reset
    document.getElementById('a11y-reset').addEventListener('click', function () {
      resetAll();
    });

    // Keep header dark-mode-toggle in sync (it may still be clicked directly)
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.dark-mode-toggle');
      if (!btn) return;
      // dark-mode.js toggles the theme; we just need to update our buttons
      setTimeout(updateModeButtons, 50);
    });
  }

  // ----------------------------------------------------------------
  // Init
  // ----------------------------------------------------------------

  function init() {
    applyStoredPreferences();
    var ui = buildToolbar();
    bindEvents(ui.fab, ui.panel);
    updateModeButtons();
    updateFontLabel();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
