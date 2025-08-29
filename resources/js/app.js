<script>
document.addEventListener('DOMContentLoaded', () => {
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const badge = document.getElementById('cart-badge');

  function setHeart(el, on) {
    const svg = el.querySelector('svg');
    svg.classList.toggle('text-red-500',  on);
    svg.classList.toggle('text-gray-600', !on);
    svg.setAttribute('fill', on ? 'currentColor' : 'none');
    el.setAttribute('aria-pressed', on ? 'true' : 'false');
  }

  function updateBadge(newWishlistCount) {
    if (!badge) return;
    const baseCart = parseInt(badge.dataset.cartCount || '0', 10);
    badge.dataset.wishlistCount = String(newWishlistCount);
    badge.textContent = String(baseCart + newWishlistCount); // cart + wishlist
  }

  window.handleWishlistToggle = async function(btn) {
    const url = btn.dataset.url;           // ← ambil dari data-url
    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json'
        }
      });
      const data = await res.json();
      if (data && data.ok) {
        setHeart(btn, data.in_wishlist);       // merah / abu
        updateBadge(data.wishlist_count);      // update badge
      }
    } catch (e) {
      console.error(e);
    }
  };

  // (opsional) binding click jika tidak pakai onclick di HTML
  document.querySelectorAll('[data-wishlist-button]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault(); e.stopPropagation();
      window.handleWishlistToggle(btn);
    });
  });
});
</script>   