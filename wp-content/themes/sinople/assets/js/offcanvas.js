/* SPDX-License-Identifier: GPL-2.0-or-later */
/**
 * Offcanvas Mobile Navigation for Sinople Theme
 *
 * Handles slide-in drawer with focus trapping and ARIA.
 */

(function () {
  'use strict';

  var drawer = document.getElementById('offcanvas-drawer');
  var backdrop = document.getElementById('offcanvas-backdrop');
  var openBtn = document.getElementById('offcanvas-open');
  var closeBtn = document.getElementById('offcanvas-close');

  if (!drawer || !backdrop || !openBtn) return;

  /**
   * Open the drawer.
   */
  function open() {
    drawer.classList.add('is-open');
    backdrop.classList.add('is-active');
    document.body.style.overflow = 'hidden';
    openBtn.setAttribute('aria-expanded', 'true');

    // Focus the close button
    if (closeBtn) {
      closeBtn.focus();
    }

    // Trap focus inside drawer
    document.addEventListener('keydown', handleKeyDown);
  }

  /**
   * Close the drawer.
   */
  function close() {
    drawer.classList.remove('is-open');
    backdrop.classList.remove('is-active');
    document.body.style.overflow = '';
    openBtn.setAttribute('aria-expanded', 'false');
    openBtn.focus();

    document.removeEventListener('keydown', handleKeyDown);
  }

  /**
   * Handle keyboard events for focus trapping and Escape.
   * @param {KeyboardEvent} e
   */
  function handleKeyDown(e) {
    if (e.key === 'Escape') {
      close();
      return;
    }

    if (e.key !== 'Tab') return;

    var focusable = drawer.querySelectorAll(
      'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
    );

    if (focusable.length === 0) return;

    var first = focusable[0];
    var last = focusable[focusable.length - 1];

    if (e.shiftKey) {
      if (document.activeElement === first) {
        e.preventDefault();
        last.focus();
      }
    } else {
      if (document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }
  }

  // Event bindings
  openBtn.addEventListener('click', function (e) {
    e.preventDefault();
    open();
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', function (e) {
      e.preventDefault();
      close();
    });
  }

  backdrop.addEventListener('click', close);
})();
