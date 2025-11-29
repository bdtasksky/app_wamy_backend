document.addEventListener("DOMContentLoaded", () => {
    const toggleBtn = document.getElementById("lang-toggle");
    const dropdown = document.getElementById("lang-menu");
    let articleNewsSwiperInstance;
    let opinionSwiperInstance;
    let categoryNewsSwiperInstance;

    function initializeSwipers(direction = "ltr") {
        // Destroy existing instances if they exist
        if (categoryNewsSwiperInstance) {
            categoryNewsSwiperInstance.destroy(true, true);
        }


        if (articleNewsSwiperInstance) {
            articleNewsSwiperInstance.destroy(true, true);
        }

        // Category News Swiper
        categoryNewsSwiperInstance = new Swiper(".CategoryNewsSlider", {
            dir: direction,
            loop: true,
            effect: "fade",
            centeredSlides: true,
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            slidesPerView: 1,
            spaceBetween: 20,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".category-swiper-button-next",
                prevEl: ".category-swiper-button-prev",
            },
        });

        // Initialize Article News Swiper
        articleNewsSwiperInstance = new Swiper(".ArticleSwiper", {
            dir: direction,
            spaceBetween: 30,
            slidesPerView: 1,
            centeredSlides: true,
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".article-swiper-button-next",
                prevEl: ".article-swiper-button-prev",
            },
        });
        // opinion Swiper
        opinionSwiperInstance = new Swiper(".OpinionFadeSwiper", {
            dir: direction,
            slidesPerView: 1,
            spaceBetween: 30,
            effect: "creative",
            creativeEffect: {
                prev: {
                    shadow: true,
                    origin: "left center",
                    translate: ["-5%", 0, -200],
                    rotate: [0, 100, 0],
                },
                next: {
                    origin: "right center",
                    translate: ["5%", 0, -200],
                    rotate: [0, -100, 0],
                },
            },
            loop: false,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".opinion-swiper-button-next",
                prevEl: ".opinion-swiper-button-prev",
            },
        });
    }

    // Initial setup
    const initialDirection = $("#direction").val() || "ltr";
    document.documentElement.setAttribute("dir", initialDirection);
    const initialLang = $("#language").val() ?? "en";
    document.documentElement.setAttribute("lang", initialLang);
    document.getElementById("lang-label").textContent = document.querySelector(
        `.lang-option[data-lang="${initialLang}"]`
    ).textContent;

    initializeSwipers(initialDirection);

    toggleBtn.addEventListener("click", () => {
        dropdown.classList.toggle("opacity-0");
        dropdown.classList.toggle("scale-95");
        dropdown.classList.toggle("translate-y-2");
        dropdown.classList.toggle("pointer-events-none");
    });

    document.addEventListener("click", (e) => {
        if (!toggleBtn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add(
                "opacity-0",
                "scale-95",
                "translate-y-2",
                "pointer-events-none"
            );
        }
    });

    document.querySelectorAll(".lang-option").forEach((option) => {
        option.addEventListener("click", (e) => {
            const selectedLang = e.target.getAttribute("data-lang");
            const baseUrl = $('meta[name="base-url"]').attr("content");
            window.location.href = `${baseUrl}/${selectedLang}`;
        });
    });
});

$(document).on("change", "#language, #language_side", function () {
    let language = $(this).val();
    const baseUrl = $('meta[name="base-url"]').attr("content");
    window.location.href = `${baseUrl}/${language}`;
});

document.getElementById('loadMoreBtn').addEventListener('click', function () {
    let btn = this;
    let offset = btn.getAttribute('data-offset');
    let loding = document.getElementById('latest-post-loding').value;
    let url = document.getElementById('latest-post-url').value;
    let load_more = document.getElementById('latest-post-load_more').value;
    let no_more_posts = document.getElementById('latest-post-no_more_posts').value;
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    btn.innerText = loding;

    fetch(url, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ offset: offset })
    })
    .then(res => res.json())
    .then(data => {
        if (data.count > 0) {
            document.querySelector('#latest-news-wrapper section')
                .insertAdjacentHTML('beforeend', data.html);
            btn.setAttribute('data-offset', parseInt(offset) + data.count);
            btn.innerText = load_more;
        } else {
            btn.innerText = no_more_posts;
            btn.disabled = true;
        }
    })
    .catch(err => {
        console.error(err);
        btn.innerText = load_more;
    });
});
