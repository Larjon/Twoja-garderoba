'use strict';

(function () {
    'use strict';
    if(thwvsf_public_var.lazy_load === 'yes'){
        var images = document.querySelectorAll('img.swatch-image[data-src]');
        document.addEventListener('DOMContentLoaded', onReady);
        function onReady() {
            // Show above-the-fold images first
            showImagesOnView();

            // scroll listener
            window.addEventListener('scroll', showImagesOnView, false);
        }

      // Show the image if reached on viewport
        function showImagesOnView(e) {

            var _iteratorNormalCompletion = true;
            var _didIteratorError = false;
            var _iteratorError = undefined;

            try {

                for (var _iterator = images[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                    var i = _step.value;
                    if (i.getAttribute('src')) {
                        continue;
                    } // SKIP if already displayed

                    // Compare the position of image and scroll
                    var bounding = i.getBoundingClientRect();
                    var isOnView = bounding.top >= 0 && bounding.left >= 0 && bounding.bottom <= (window.innerHeight || document.documentElement.clientHeight) && bounding.right <= (window.innerWidth || document.documentElement.clientWidth);

                    if (isOnView) {
                        i.setAttribute('src', i.dataset.src);
                        if (i.getAttribute('data-srcset')) {
                        i.setAttribute('srcset', i.dataset.srcset);
                      }
                    }
                }
            } catch (err) {
                _didIteratorError = true;
                _iteratorError = err;
            } finally {
                try {
                    if (!_iteratorNormalCompletion && _iterator.return) {
                        _iterator.return();
                    }
                } finally {
                    if (_didIteratorError) {
                    throw _iteratorError;
                    }
                }
            }
        }
    }
})();