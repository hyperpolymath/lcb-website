/* SPDX-License-Identifier: GPL-2.0-or-later */
/**
 * Search Modal for Sinople Theme
 *
 * Full-screen overlay with focus trapping and keyboard support.
 */

(function () {
  'use strict';

  var modal = document.getElementById('search-modal');
  if (!modal) return;

  var closeBtn = modal.querySelector('.search-modal-close');
  var searchField = modal.querySelector('.search-field');

  /**
   * Open the search modal.
   */
  function open() {
    modal.classList.add('is-active');
    document.body.style.overflow = 'hidden';

    // Focus the search input
    if (searchField) {
      setTimeout(function () { searchField.focus(); }, 100);
    }

    document.addEventListener('keydown', handleKeyDown);
  }

  /**
   * Close the search modal.
   */
  function close() {
    modal.classList.remove('is-active');
    document.body.style.overflow = '';
    document.removeEventListener('keydown', handleKeyDown);

    // Return focus to trigger button
    var trigger = document.querySelector('[data-search-open]');
    if (trigger) trigger.focus();
  }

  /**
   * Handle keyboard events.
   * @param {KeyboardEvent} e
   */
  function handleKeyDown(e) {
    if (e.key === 'Escape') {
      close();
      return;
    }

    // Focus trap
    if (e.key !== 'Tab') return;

    var focusable = modal.querySelectorAll(
      'input:not([disabled]), button:not([disabled]), [tabindex]:not([tabindex="-1"])'
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

  // Bind triggers
  document.addEventListener('click', function (e) {
    var trigger = e.target.closest('[data-search-open]');
    if (trigger) {
      e.preventDefault();
      open();
    }
  });

  // Close button
  if (closeBtn) {
    closeBtn.addEventListener('click', function (e) {
      e.preventDefault();
      close();
    });
  }

  // Click outside content to close
  modal.addEventListener('click', function (e) {
    if (e.target === modal) {
      close();
    }
  });

  // Keyboard shortcut: Ctrl+K or / to open
  document.addEventListener('keydown', function (e) {
    // Don't trigger when typing in inputs
    var tag = (e.target.tagName || '').toLowerCase();
    if (tag === 'input' || tag === 'textarea' || tag === 'select') return;
    if (e.target.isContentEditable) return;

    if ((e.ctrlKey && e.key === 'k') || (e.key === '/' && !e.ctrlKey && !e.metaKey)) {
      e.preventDefault();
      open();
    }
  });
})();
