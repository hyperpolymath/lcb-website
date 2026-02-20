/* SPDX-License-Identifier: GPL-2.0-or-later */
/**
 * Navigation and Accessibility JavaScript for Sinople Theme
 *
 * Handles:
 * - Keyboard navigation (Alt+1, Alt+2, arrow keys, Escape)
 * - Skip links
 * - Desktop dropdown menus
 * - Focus management
 * - Screen reader announcements
 *
 * @package Sinople
 * @since 2.0.0
 */

(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    initKeyboardNavigation();
    initSkipLinks();
    initDropdowns();
  });

  /**
   * Keyboard Navigation Enhancements
   */
  function initKeyboardNavigation() {
    document.addEventListener('keydown', function (e) {
      // Alt+1: Skip to main content
      // Alt+2: Skip to navigation
      if (e.altKey) {
        switch (e.key) {
          case '1':
            e.preventDefault();
            focusElement('#main');
            break;
          case '2':
            e.preventDefault();
            focusElement('#nav');
            break;
        }
      }
    });

    // Arrow key navigation in menus
    var menuItems = document.querySelectorAll('.main-navigation .primary-menu > li > a');
    for (var i = 0; i < menuItems.length; i++) {
      (function (index) {
        menuItems[index].addEventListener('keydown', function (e) {
          var targetIndex;
          switch (e.key) {
            case 'ArrowRight':
              e.preventDefault();
              targetIndex = index + 1;
              if (targetIndex < menuItems.length) menuItems[targetIndex].focus();
              break;
            case 'ArrowLeft':
              e.preventDefault();
              targetIndex = index - 1;
              if (targetIndex >= 0) menuItems[targetIndex].focus();
              break;
            case 'Home':
              e.preventDefault();
              menuItems[0].focus();
              break;
            case 'End':
              e.preventDefault();
              menuItems[menuItems.length - 1].focus();
              break;
          }
        });
      })(i);
    }
  }

  /**
   * Skip Links Focus Management
   */
  function initSkipLinks() {
    var skipLinks = document.querySelectorAll('.skip-link');
    for (var i = 0; i < skipLinks.length; i++) {
      skipLinks[i].addEventListener('click', function (e) {
        var target = document.querySelector(this.getAttribute('href'));
        if (target) {
          e.preventDefault();
          target.setAttribute('tabindex', '-1');
          target.focus();
          target.addEventListener('blur', function () {
            this.removeAttribute('tabindex');
          }, { once: true });
        }
      });
    }
  }

  /**
   * Desktop Dropdown Menus
   */
  function initDropdowns() {
    var toggles = document.querySelectorAll('.dropdown-toggle');
    for (var i = 0; i < toggles.length; i++) {
      toggles[i].addEventListener('click', function (e) {
        e.preventDefault();
        var expanded = this.getAttribute('aria-expanded') === 'true';
        // Close all other dropdowns
        for (var j = 0; j < toggles.length; j++) {
          toggles[j].setAttribute('aria-expanded', 'false');
        }
        this.setAttribute('aria-expanded', expanded ? 'false' : 'true');
      });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function (e) {
      if (!e.target.closest('.has-dropdown')) {
        for (var j = 0; j < toggles.length; j++) {
          toggles[j].setAttribute('aria-expanded', 'false');
        }
      }
    });

    // Escape closes dropdowns
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        for (var j = 0; j < toggles.length; j++) {
          if (toggles[j].getAttribute('aria-expanded') === 'true') {
            toggles[j].setAttribute('aria-expanded', 'false');
            toggles[j].closest('.has-dropdown').querySelector('a').focus();
          }
        }
      }
    });
  }

  /**
   * Helper: Focus Element by Selector
   */
  function focusElement(selector) {
    var element = document.querySelector(selector);
    if (element) {
      element.setAttribute('tabindex', '-1');
      element.focus();
      element.addEventListener('blur', function () {
        this.removeAttribute('tabindex');
      }, { once: true });
    }
  }

  /**
   * Announce to Screen Readers
   */
  function announceToScreenReader(message) {
    var liveRegion = document.getElementById('aria-live-region');
    if (!liveRegion) {
      liveRegion = document.createElement('div');
      liveRegion.id = 'aria-live-region';
      liveRegion.setAttribute('aria-live', 'polite');
      liveRegion.setAttribute('aria-atomic', 'true');
      liveRegion.className = 'screen-reader-text';
      document.body.appendChild(liveRegion);
    }
    liveRegion.textContent = message;
    setTimeout(function () { liveRegion.textContent = ''; }, 1000);
  }

  window.sinople = window.sinople || {};
  window.sinople.announceToScreenReader = announceToScreenReader;
})();
