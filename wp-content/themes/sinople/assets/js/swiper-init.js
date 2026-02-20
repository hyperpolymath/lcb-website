/* SPDX-License-Identifier: GPL-2.0-or-later */
/**
 * Swiper Carousel Initialisation for Sinople Theme
 */

(function () {
  'use strict';

  var el = document.querySelector('.featured-carousel .swiper');
  if (!el || typeof Swiper === 'undefined') return;

  new Swiper(el, {
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: true,
      pauseOnMouseEnter: true,
    },
    effect: 'fade',
    fadeEffect: {
      crossFade: true,
    },
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    keyboard: {
      enabled: true,
      onlyInViewport: true,
    },
    a11y: {
      prevSlideMessage: 'Previous slide',
      nextSlideMessage: 'Next slide',
      firstSlideMessage: 'This is the first slide',
      lastSlideMessage: 'This is the last slide',
      paginationBulletMessage: 'Go to slide {{index}}',
    },
  });
})();
