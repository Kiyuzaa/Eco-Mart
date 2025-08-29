

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>EcoMart — Sustainable Store</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
    <style>
    html {
  scroll-behavior: smooth;
}
</style>

        <style>
            /* Minimal fallback so layout still looks fine without Vite during quick preview */
            @import url('https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap');
            html,body{font-family:Figtree,system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;background:#F9FAFB;color:#0F172A;}
            .container{max-width:1400px;margin: auto;padding:0.5rem}
            .grid{display:grid;gap:1rem}
            .columns-2{grid-template-columns:1fr 1fr}
            .columns-3{grid-template-columns:repeat(3,1fr)}
            .card{background:#fff;border:1px solid #E5E7EB;border-radius:8px;padding:1rem}
            .muted{color:#6B7280}
            a.button{display:inline-block;padding:.6rem 1rem;border-radius:8px;text-decoration:none}
            a.primary{background:#0F172A;color:#fff}
            a.ghost{border:1px solid #E5E7EB;color:#0F172A}
        </style>

</head>


<body class="antialiased selection:bg-[#FF2D20] selection:text-white">
    <x-navbar />    
    <div class="container">
        {{-- Navbar --}}


        <!-- Hero -->
        <main class="grid columns-2" style="background:#f9fafb;center;margin-top:3rem;gap:2rem; margin-bottom:3rem">
            <section id="home">
                <h1 style="font-size:4rem;line-height:1.05;font-weight:600;">Shop Sustainable, <br>Live Better</h1>
                <p class="muted" style="margin-top:.75rem;max-width:45ch;">
                    Discover eco-friendly products that make a positive impact on our planet. From zero‑waste essentials to sustainable fashion and home goods.
                </p>

                <div style="margin-top:1rem;display:flex;gap:.75rem;flex-wrap:wrap;">
                    <a href="#" class="button primary">Shop Now</a>
                    <a href="#" class="button ghost">Learn More</a>
                </div>

                <div style="display:flex;gap:1.25rem;margin-top:1rem;color:#6B7280;font-size:.9rem">
                    <div><strong>50k+</strong><div class="muted">Happy Customers</div></div>
                    <div><strong>95%</strong><div class="muted">Satisfaction</div></div>
                    <div><strong>1M+</strong><div class="muted">Products Delivered</div></div>
                </div>
            </section>

            <section>
                <div style="border-radius:10px;overflow:hidden;border:1px solid #E5E7EB;background:#fff;height:320px;display:flex;align-items:center;justify-content:center;">
                    @if(!empty($products) && isset($products[0]['image']))
                        <img src="{{ asset('storage/'.$products[0]['image']) }}" alt="hero product mockup" style="width:100%;height:100%;object-fit:cover;object-position:center"/>
                    @else
                        <img src="https://images.unsplash.com/photo-1542444459-db3d6f1e6e9f?q=80&w=1200&auto=format&fit=crop" alt="hero product mockup" style="width:100%;height:100%;object-fit:cover;object-position:center"/>
                    @endif
                </div>
            </section>
        </main>

        <!-- Categories -->

        <section id="shop" style="background:#ffffff;margin-top:3rem;">
            <h3 style="text-align:center;color:#000000;font-size:2rem;font-weight:600">Shop by Category</h3>
            <p style="text-align:center;color:#9CA3AF;margin-top:.5rem">Explore our curated collection of sustainable products.</p>

            <div class="grid columns-4" style="grid-template-columns:repeat(4,1fr);gap:1rem;margin-top:2rem;min-width:200px; margin-bottom: 2rem;">
                @forelse($categories as $cat)
                    <div class="card" style="text-align:center">
                        <div style="font-size:1.75rem; height: 10rem; background: #e5e7eb" ></div>
                        <div style="font-weight:600;margin-top:.5rem">{{ ucwords(str_replace('-', ' ', $cat)) }}</div>
                        <div class="muted" style="margin-top:.5rem;font-size:.85rem">Products from <br>{{ ucwords(str_replace('-', ' ', $cat)) }}</div>
                    </div>
                @empty
                    <div class="card" style="text-align:center;">
                        <div style="font-weight:600">General</div>
                        <div class="muted" style="margin-top:.5rem;font-size:.85rem">Browse our featured items</div>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Featured Products -->
        <section id="blog" style="background:#f9fafb;margin-top:2rem; margin-bottom:2rem;">
    <h3 style="text-align:center;font-size:2rem;font-weight:600">Featured Products</h3>
    <p style="text-align:center;color:#9CA3AF;margin-top:.25rem">Our most popular eco-friendly items</p>

    <div class="grid columns-3" style="grid-template-columns:repeat(3,1fr);gap:1rem;margin-top:1rem">
        @forelse($products as $p)
            <x-product-card :product="$p" />
        @empty
            <div class="card">No products available.</div>
        @endforelse
    </div>
</section>


        <!-- Assistant / Contact -->
<section id="assistant" class="mt-8 bg-gray-50 rounded-lg p-6">
  <div class="flex flex-col md:flex-row gap-8 items-start">

    <!-- Left Side -->
    <div class="flex-1 md:max-w-[70%]">
      <h2 class="text-2xl font-semibold">Meet Your Eco Shopping Assistant</h2>
      <p class="text-gray-600 mt-2">
        Our AI-powered chatbot helps you find the perfect sustainable products based on your lifestyle and preferences.
      </p>

      <ul class="mt-4 space-y-2 text-gray-700">
        <li class="flex items-center gap-2">
          <span class="text-green-600">✔</span> Personalized product recommendations
        </li>
        <li class="flex items-center gap-2">
          <span class="text-green-600">✔</span> Eco-impact comparisons
        </li>
        <li class="flex items-center gap-2">
          <span class="text-green-600">✔</span> Sustainability tips and advice
        </li>
      </ul>

      <button type="button"
        class="mt-5 text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300
               font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:hover:bg-gray-700">
        Start Chatting
      </button>
    </div>

    <!-- Right Side (Chat UI) -->
    <div class="flex-1 md:max-w-[30%] border border-gray-200 rounded-xl shadow p-4 bg-white">
      <div class="space-y-3 text-sm">

        <!-- Chat bubble assistant -->
        <div class="bg-gray-100 p-3 rounded-lg">
          <p class="font-medium">🤖 Hi! I'm your eco shopping assistant. What kind of sustainable products are you looking for today?</p>
        </div>

        <!-- Chat bubble user -->
        <div class="bg-green-50 p-3 rounded-lg self-end">
          <p>I'm looking for eco-friendly clothing for teenagers</p>
        </div>

        <!-- Chat bubble assistant -->
        <div class="bg-gray-100 p-3 rounded-lg">
          <p>Great choice! I recommend checking out our organic cotton t-shirts and recycled denim collection. They're perfect for teens and have a 90%+ eco score!</p>
        </div>
      </div>

      <!-- Input -->
      <div class="mt-4 flex gap-2">
        <input type="text" placeholder="Type your message..."
          class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-green-200" />
        <button class="bg-black text-white px-3 py-2 rounded-lg hover:bg-gray-800">➤</button>
      </div>
    </div>

  </div>
</section>




        <!-- Testimonials -->
        <section style="margin-top:2rem; margin-bottom: 2rem">
            <h4 style="text-align:center;font-weight:600">What Our Customers Say</h4>
            <div class="grid columns-3" style="grid-template-columns:repeat(3,1fr);gap:1rem;margin-top:1rem">
                @forelse($testimonials as $t)
                    <div class="card">
                        <div style="display:flex;gap:.75rem;align-items:flex-start">
                            <img src="{{ $t['avatar'] }}" alt="{{ $t['name'] }}" style="width:44px;height:44px;border-radius:999px;object-fit:cover">
                            <div>
                                <div style="font-weight:600">{{ $t['name'] }}</div>
                                <p class="muted" style="margin-top:.4rem">{{ $t['text'] }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card">No testimonials yet.</div>
                @endforelse
            </div>
        </section>


        {{-- Footer --}}

    </div>
    <x-footer />
</body>
</html>
