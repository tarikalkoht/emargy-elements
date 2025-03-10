/**
 * Timeline Slider JS - Updated for Arabic RTL support
 */
(function($) {
    'use strict';

    /**
     * Timeline Slider Class
     */
    class EmargyTimelineSlider {
        constructor(element) {
            // العناصر
            this.slider = $(element);
            this.slidesWrapper = this.slider.find('.emargy-slider-wrapper');
            this.slides = this.slider.find('.emargy-timeline-slide');
            this.markers = this.slider.find('.emargy-timeline-marker');
            this.numbers = this.slider.find('.emargy-timeline-number');
            this.handle = this.slider.find('.emargy-timeline-handle');
            this.timelineBar = this.slider.find('.emargy-timeline-bar');
            
            // الإعدادات
            this.slidesCount = this.slides.length;
            this.currentIndex = 0;
            this.isDragging = false;
            this.startX = 0;
            this.autoplayTimer = null;
            
            // الحصول على الإعدادات من سمات البيانات
            this.autoplay = this.slider.data('autoplay') === 'yes';
            this.autoplaySpeed = parseInt(this.slider.data('autoplay-speed'), 10) || 5000;
            this.pauseOnHover = this.slider.data('pause-on-hover') === 'yes';
            
            // الحصول على فهرس الشريحة النشطة
            const activeSlide = this.slides.filter('.active');
            if (activeSlide.length) {
                this.currentIndex = activeSlide.data('index') - 1;
            } else {
                // تعيين الشريحة السادسة (الفهرس 5) كنشطة افتراضيًا إذا لم يتم تحديد أي منها
                this.currentIndex = Math.min(5, this.slidesCount - 1);
                this.slides.eq(this.currentIndex).addClass('active');
            }
            
            // تهيئة السلايدر
            this.init();
        }
        
        /**
         * تهيئة السلايدر
         */
        init() {
            if (this.slidesCount <= 1) {
                return;
            }
            
            // تحديث أرقام التايملاين لتكون مثل 01، 02، إلخ
            this.updateTimelineNumbers();
            
            // تحديث موضع المؤشر الأولي
            this.updateHandlePosition(this.currentIndex);
            
            // ربط الأحداث
            this.bindEvents();
            
            // بدء التشغيل التلقائي إذا كان ممكّنًا
            if (this.autoplay) {
                this.startAutoplay();
            }
            
            // الموضع الأولي
            this.goToSlide(this.currentIndex, false);
        }
        
        /**
         * تحديث أرقام التايملاين
         */
        updateTimelineNumbers() {
            // إضافة ترقيم من 01 إلى XX
            this.numbers.each(function(index) {
                // تنسيق الرقم بإضافة صفر للأرقام أقل من 10
                const formattedNumber = (index + 1) < 10 ? 
                    '0' + (index + 1) : 
                    '' + (index + 1);
                $(this).text(formattedNumber);
            });
        }
        
        /**
         * ربط الأحداث
         */
        bindEvents() {
            // أحداث سحب المؤشر
            this.timelineBar.on('mousedown touchstart', this.onTimelineBarClick.bind(this));
            this.handle.on('mousedown touchstart', this.onHandleDragStart.bind(this));
            $(document).on('mousemove touchmove', this.onHandleDragMove.bind(this));
            $(document).on('mouseup touchend', this.onHandleDragEnd.bind(this));
            
            // أحداث النقر على العلامات
            this.markers.on('click', this.onMarkerClick.bind(this));
            
            // أحداث النقر على الأرقام
            this.numbers.on('click', this.onNumberClick.bind(this));
            
            // أحداث النقر على الشرائح
            this.slides.on('click', this.onSlideClick.bind(this));
            
            // إيقاف التشغيل التلقائي عند التمرير فوق العنصر إذا كان ممكّنًا
            if (this.pauseOnHover) {
                this.slider.on('mouseenter', this.pauseAutoplay.bind(this));
                this.slider.on('mouseleave', this.resumeAutoplay.bind(this));
            }
            
            // التعامل مع تغيير حجم النافذة
            $(window).on('resize', this.onResize.bind(this));

            // إضافة مستمعات النقر لتبديل الفئة النشطة
            const slides = document.querySelectorAll(".emargy-timeline-slide");
            slides.forEach((slide, index) => {
                slide.addEventListener("click", () => {
                    slides.forEach(s => s.classList.remove("active"));
                    slide.classList.add("active");
                });
            });
        }
        
        /**
         * معالجة النقر على شريط التايملاين
         */
        onTimelineBarClick(e) {
            if (this.isDragging) return;
            
            const pageX = e.pageX || e.originalEvent.touches[0].pageX;
            const barWidth = this.timelineBar.width();
            const barOffset = this.timelineBar.offset().left;
            const relativeX = pageX - barOffset;
            
            // حساب النسبة المئوية للموضع (مقيدة بين 0 و 100)
            let positionPercent = Math.max(0, Math.min(100, (relativeX / barWidth) * 100));
            
            // حساب فهرس الشريحة الأقرب
            const slideIndex = Math.round((positionPercent / 100) * (this.slidesCount - 1));
            
            // الانتقال إلى الشريحة
            this.goToSlide(slideIndex);
            
            // إيقاف وإعادة تشغيل التشغيل التلقائي
            this.pauseAutoplay();
            setTimeout(() => {
                this.resumeAutoplay();
            }, 300);
        }
        
        /**
         * التعامل مع تغيير حجم النافذة
         */
        onResize() {
            this.goToSlide(this.currentIndex, false);
        }
        
        /**
         * الانتقال إلى شريحة محددة
         * 
         * @param {number} index - فهرس الشريحة
         * @param {boolean} animate - ما إذا كان سيتم تحريك الانتقال
         */
        goToSlide(index, animate = true) {
            if (index < 0) index = 0;
            if (index >= this.slidesCount) index = this.slidesCount - 1;
            
            this.currentIndex = index;
            
            const $activeSlide = this.slides.eq(index);
            const sliderWidth = this.slider.width();
            const slideWidth = $activeSlide.outerWidth(true);
            const slideLeft = $activeSlide.position().left;
            
            // Calculate center position for the active slide
            const centerOffset = (sliderWidth / 2) - (slideWidth / 2) - slideLeft;

            if (animate) {
                this.slidesWrapper.css('transition', `transform 0.3s ease-out`);
            } else {
                this.slidesWrapper.css('transition', 'none');
            }
            
            this.slidesWrapper.css('transform', `translateX(${centerOffset}px)`);
            
            // Update active classes
            this.slides.removeClass('active');
            this.slides.eq(index).addClass('active');
            
            // Update handle position
            this.updateHandlePosition(index);
        }
        
        /**
         * تحديث موضع المؤشر
         * 
         * @param {number} index - فهرس الشريحة
         */
        updateHandlePosition(index) {
            const position = this.slidesCount > 1 
                ? (index / (this.slidesCount - 1)) * 100 
                : 50;
                
            this.handle.css('left', `${position}%`);
        }
        
        /**
         * بدء التشغيل التلقائي
         */
        startAutoplay() {
            if (!this.autoplay) {
                return;
            }
            
            this.autoplayTimer = setInterval(() => {
                let nextIndex = this.currentIndex + 1;
                if (nextIndex >= this.slidesCount) {
                    nextIndex = 0;
                }
                
                this.goToSlide(nextIndex);
            }, this.autoplaySpeed);
        }
        
        /**
         * إيقاف التشغيل التلقائي
         */
        pauseAutoplay() {
            if (this.autoplayTimer) {
                clearInterval(this.autoplayTimer);
                this.autoplayTimer = null;
            }
        }
        
        /**
         * استئناف التشغيل التلقائي
         */
        resumeAutoplay() {
            if (this.autoplay && !this.isDragging) {
                this.startAutoplay();
            }
        }
        
        /**
         * معالجة بدء سحب المؤشر
         * 
         * @param {Event} e - كائن الحدث
         */
        onHandleDragStart(e) {
            e.preventDefault();
            
            this.isDragging = true;
            this.startX = e.pageX || e.originalEvent.touches[0].pageX;
            
            // إيقاف التشغيل التلقائي أثناء السحب
            this.pauseAutoplay();
            
            // إضافة فئة السحب إلى المؤشر
            this.handle.addClass('dragging');
        }
        
        /**
         * معالجة تحريك السحب
         * 
         * @param {Event} e - كائن الحدث
         */
        onHandleDragMove(e) {
            if (!this.isDragging) {
                return;
            }
            
            e.preventDefault();
            
            const pageX = e.pageX || e.originalEvent.touches[0].pageX;
            const barWidth = this.timelineBar.width();
            const barOffset = this.timelineBar.offset().left;
            const relativeX = pageX - barOffset;
            
            // حساب النسبة المئوية للموضع (مقيدة بين 0 و 100)
            let positionPercent = Math.max(0, Math.min(100, (relativeX / barWidth) * 100));
            
            // تحديث موضع المؤشر
            this.handle.css('left', `${positionPercent}%`);
            
            // حساب فهرس الشريحة الأقرب
            const slideIndex = Math.round((positionPercent / 100) * (this.slidesCount - 1));
            
            // إذا تغير فهرس الشريحة، انتقل إلى الشريحة الجديدة
            if (slideIndex !== this.currentIndex) {
                this.goToSlide(slideIndex);
            }
        }
        
        /**
         * معالجة نهاية السحب
         */
        onHandleDragEnd() {
            if (!this.isDragging) {
                return;
            }
            
            this.isDragging = false;
            
            // إزالة فئة السحب من المؤشر
            this.handle.removeClass('dragging');
            
            // التقاط إلى أقرب شريحة
            const handlePosition = parseFloat(this.handle.css('left')) / this.timelineBar.width() * 100;
            const slideIndex = Math.round((handlePosition / 100) * (this.slidesCount - 1));
            
            this.goToSlide(slideIndex);
            
            // استئناف التشغيل التلقائي إذا كان ممكّنًا
            setTimeout(() => {
                this.resumeAutoplay();
            }, 300);
        }
        
        /**
         * معالجة النقر على العلامة
         * 
         * @param {Event} e - كائن الحدث
         */
        onMarkerClick(e) {
            const marker = $(e.currentTarget);
            const index = marker.data('index') - 1;
            
            this.goToSlide(index);
            
            // إيقاف واستئناف التشغيل التلقائي
            this.pauseAutoplay();
            setTimeout(() => {
                this.resumeAutoplay();
            }, 300);
        }
        
        /**
         * معالجة النقر على الرقم
         * 
         * @param {Event} e - كائن الحدث
         */
        onNumberClick(e) {
            const number = $(e.currentTarget);
            const index = number.data('index') - 1;
            
            this.goToSlide(index);
            
            // إيقاف واستئناف التشغيل التلقائي
            this.pauseAutoplay();
            setTimeout(() => {
                this.resumeAutoplay();
            }, 300);
        }
        
        /**
         * معالجة النقر على الشريحة
         * 
         * @param {Event} e - كائن الحدث
         */
        onSlideClick(e) {
            const slide = $(e.currentTarget);
            const index = slide.data('index') - 1;
            
            // إذا تم النقر على الشريحة النشطة، يمكن إضافة سلوك تشغيل الفيديو هنا
            if (index === this.currentIndex) {
                // على سبيل المثال: تشغيل الفيديو أو تنفيذ إجراء آخر
                console.log('تم النقر على الشريحة النشطة!');
                return;
            }
            
            this.goToSlide(index);
            
            // إيقاف واستئناف التشغيل التلقائي
            this.pauseAutoplay();
            setTimeout(() => {
                this.resumeAutoplay();
            }, 300);
        }
    }
    
    // تهيئة جميع سلايدرات التايملاين في الصفحة
    $(document).ready(function() {
        $('.emargy-timeline-slider').each(function() {
            new EmargyTimelineSlider(this);
        });
    });
    
})(jQuery);