// resources/js/app.js
import "./bootstrap";

// Jalankan setelah DOM siap
document.addEventListener("DOMContentLoaded", () => {
    // ====== HAMBURGER / MENU MOBILE ======
    const btn = document.getElementById("hamburger-btn");
    const menu = document.getElementById("mobile-menu");
    const openIcon = document.getElementById("icon-open");
    const closeIcon = document.getElementById("icon-close");

    function syncMenuState() {
        if (!btn || !menu) return;
        const opened = !menu.classList.contains("hidden");
        if (openIcon) openIcon.classList.toggle("hidden", opened);
        if (closeIcon) closeIcon.classList.toggle("hidden", !opened);
        btn.setAttribute("aria-expanded", opened ? "true" : "false");
        // kunci scroll saat drawer terbuka (opsional)
        document.documentElement.classList.toggle("overflow-hidden", opened);
        document.body.classList.toggle("overflow-hidden", opened);
    }

    if (btn && menu) {
        syncMenuState();
        btn.addEventListener("click", () => {
            menu.classList.toggle("hidden");
            syncMenuState();
        });
        document.addEventListener("click", (e) => {
            if (
                !btn.contains(e.target) &&
                !menu.contains(e.target) &&
                !menu.classList.contains("hidden")
            ) {
                menu.classList.add("hidden");
                syncMenuState();
            }
        });
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && !menu.classList.contains("hidden")) {
                menu.classList.add("hidden");
                syncMenuState();
            }
        });
    }

    // ====== GLOBAL HELPER BADGE (jaga-jaga kalau belum ada) ======
    if (typeof window.updateCartBadge !== "function") {
        window.updateCartBadge = function (count) {
            const el = document.getElementById("cart-badge");
            if (!el) return;
            const n = Number(count ?? 0);
            el.textContent = n;
            el.style.display = n > 0 ? "inline-flex" : "none";
        };
    }
    if (typeof window.updateWishlistBadge !== "function") {
        window.updateWishlistBadge = function (count) {
            const el = document.getElementById("wishlist-badge");
            if (!el) return;
            const n = Number(count ?? 0);
            el.textContent = n;
            el.style.display = n > 0 ? "inline-flex" : "none";
        };
    }

    // ====== TOGGLE WISHLIST (boleh dihapus jika sudah ada di Blade @push) ======
    if (typeof window.ecoWishlistToggle !== "function") {
        window.ecoWishlistToggle = async function (btn) {
            const url = btn?.dataset?.url;
            const pid = btn?.dataset?.productId;
            if (!url) return;

            try {
                const res = await fetch(url, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN":
                            document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute("content") || "",
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                    credentials: "same-origin",
                });
                if (!res.ok) return;
                const data = await res.json();
                const added = data?.state === "added" || !!data?.added;

                document
                    .querySelectorAll(
                        `[data-wishlist-button][data-product-id="${pid}"]`
                    )
                    .forEach((el) => {
                        const svg = el.querySelector("svg");
                        if (!svg) return;
                        svg.classList.toggle("text-pink-600", added);
                        svg.classList.toggle("text-gray-600", !added);
                        svg.setAttribute(
                            "fill",
                            added ? "currentColor" : "none"
                        );
                        el.setAttribute(
                            "aria-pressed",
                            added ? "true" : "false"
                        );
                        el.setAttribute(
                            "aria-label",
                            added ? "Hapus dari Wishlist" : "Tambah ke Wishlist"
                        );
                    });

                const wlCount = Number(data?.wishlist_count ?? 0);
                window.updateWishlistBadge(wlCount);
            } catch (e) {
                console.error(e);
            }
        };
    }
});
