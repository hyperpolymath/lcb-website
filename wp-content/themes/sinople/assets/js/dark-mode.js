/* SPDX-License-Identifier: GPL-2.0-or-later */
/**
 * Dark Mode Toggle for Sinople Theme
 *
 * Reads preference from: localStorage → OS media query → default light.
 * Persists choice to localStorage.
 */

(function () {
  'use strict';

  var STORAGE_KEY = 'sinople_theme';
  var html = document.documentElement;

  /**
   * Get the current theme from the DOM attribute.
   * @returns {'light'|'dark'}
   */
  function getCurrentTheme() {
    return html.getAttribute('data-theme') || 'light';
  }

  /**
   * Apply a theme to the document and persist.
   * @param {'light'|'dark'} theme
   */
  function setTheme(theme) {
    html.setAttribute('data-theme', theme);
    try {
      localStorage.setItem(STORAGE_KEY, theme);
    } catch (e) {
      // localStorage unavailable (private browsing, storage full)
    }
    updateAriaLabels(theme);
  }

  /**
   * Toggle between light and dark.
   */
  function toggleTheme() {
    var next = getCurrentTheme() === 'dark' ? 'light' : 'dark';
    setTheme(next);
  }

  /**
   * Update ARIA labels on all toggle buttons.
   * @param {'light'|'dark'} theme
   */
  function updateAriaLabels(theme) {
    var buttons = document.querySelectorAll('.dark-mode-toggle');
    var label = theme === 'dark'
      ? 'Switch to light mode'
      : 'Switch to dark mode';

    for (var i = 0; i < buttons.length; i++) {
      buttons[i].setAttribute('aria-label', label);
    }
  }

  /**
   * Listen for OS preference changes.
   */
  function watchOsPreference() {
    if (!window.matchMedia) return;

    var mq = window.matchMedia('(prefers-color-scheme: dark)');
    mq.addEventListener('change', function (e) {
      // Only follow OS if user hasn't manually chosen
      var stored = null;
      try { stored = localStorage.getItem(STORAGE_KEY); } catch (ex) {}
      if (!stored) {
        setTheme(e.matches ? 'dark' : 'light');
      }
    });
  }

  /**
   * Initialise: bind click handlers and set initial ARIA.
   */
  function init() {
    // Bind all toggle buttons
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.dark-mode-toggle');
      if (btn) {
        e.preventDefault();
        toggleTheme();
      }
    });

    // Set initial ARIA state
    updateAriaLabels(getCurrentTheme());

    // Watch OS preference changes
    watchOsPreference();
  }

  // Run on DOMContentLoaded (or immediately if already loaded)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
