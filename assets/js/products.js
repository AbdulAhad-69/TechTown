const products = [
    {
        id: 1,
        name: "iPhone 13 Pro Max",
        category: "Smartphones", price: 67000,
        condition: "Used - Like New",
        image: "assets/images/Apple-iPhone-13-Pro-Max.jpg",
        desc: "Battery Health 98%. Comes with box and cable."
    },
    {
        id: 2,
        name: "Samsung S23 Ultra",
        category: "Smartphones",
        price: 85500,
        condition: "Used - Good",
        image: "assets/images/Samsung-Galaxy-S23-Ultra.jpg",
        desc: "Korean variant. Minor scratches on bezel."
    },
    {
        id: 3,
        name: "Google Pixel 7 Pro",
        category: "Smartphones",
        price: 55000,
        condition: "Used - Fair",
        image: "assets/images/Google-Pixel-7-Pro.jpg",
        desc: "Device only. No issues with camera."
    },
    {
        id: 4,
        name: "OnePlus 11 5G",
        category: "Smartphones",
        price: 52000,
        condition: "Used - Like New",
        image: "assets/images/OnePlus-11-5G.jpg",
        desc: "Full box available. 12/256GB variant."
    },
    {
        id: 5,
        name: "Xiaomi 13 Ultra",
        category: "Smartphones",
        price: 80000,
        condition: "Used - Good",
        image: "assets/images/Xiaomi-13-Ultra.jpg",
        desc: "Camera beast. Minor usage signs."
    },
    {
        id: 6,
        name: "MacBook Air M2",
        category: "Laptops",
        price: 91000,
        condition: "New",
        image: "assets/images/MacBook-Air-M2.jpg",
        desc: "Brand new sealed pack. 1 Year Apple Warranty."
    },
    {
        id: 7,
        name: "Dell XPS 13",
        category: "Laptops",
        price: 215000,
        condition: "New",
        image: "assets/images/Dell-XPS-13.jpg",
        desc: "Latest gen, OLED screen."
    },
    {
        id: 8,
        name: "HP Spectre x360",
        category: "Laptops",
        price: 140000,
        condition: "New",
        image: "assets/images/HP-Spectre-x360.jpg",
        desc: "Convertible laptop with pen included."
    },
    {
        id: 9,
        name: "Lenovo ThinkPad X1",
        category: "Laptops",
        price: 165000,
        condition: "New",
        image: "assets/images/Lenovo-ThinkPad-X1.jpg",
        desc: "Business class durability."
    }
];

// 2. LOGIC FOR SHOP PAGE
const grid = document.getElementById('product-grid');

if (grid) {
    function render(items) {
        if (items.length === 0) {
            grid.innerHTML = '<p style="text-align:center; grid-column:1/-1;">No products found matching your search.</p>';
            document.getElementById('count').innerText = 0;
            return;
        }

        grid.innerHTML = items.map(p => `
            <a href="product-details.html?id=${p.id}" class="product-card">
                <div class="product-img">
                    <img src="${p.image}" alt="${p.name}">
                </div>
                <div class="product-info">
                    <h3>${p.name}</h3>
                    <div class="product-meta">${p.category} | ${p.condition}</div>
                    <div class="product-price">৳ ${p.price.toLocaleString()}</div>
                </div>
            </a>
        `).join('');
        document.getElementById('count').innerText = items.length;
    }

    function filterAndSort() {
        // 1. Get Values
        const searchInput = document.querySelector('.search-bar');
        const searchQuery = searchInput ? searchInput.value.toLowerCase() : '';

        const cats = [...document.querySelectorAll('.filter-cat:checked')].map(c => c.value);
        const conds = [...document.querySelectorAll('.filter-cond:checked')].map(c => c.value);
        const min = parseInt(document.getElementById('min').value) || 0;
        const max = parseInt(document.getElementById('max').value) || 9999999;
        const sortValue = document.getElementById('sort-select').value;

        // 2. Filter Array
        let filtered = products.filter(p => {
            // Search Match (Name or Description)
            const matchesSearch = p.name.toLowerCase().includes(searchQuery) ||
                p.desc.toLowerCase().includes(searchQuery);

            return matchesSearch &&
                (cats.length === 0 || cats.includes(p.category)) &&
                (conds.length === 0 || conds.includes(p.condition)) &&
                (p.price >= min && p.price <= max);
        });

        // 3. Sort Array
        if (sortValue === 'low-high') {
            filtered.sort((a, b) => a.price - b.price);
        } else if (sortValue === 'high-low') {
            filtered.sort((a, b) => b.price - a.price);
        }

        render(filtered);
    }

    // Attach Listeners
    document.querySelectorAll('input').forEach(i => i.addEventListener('change', filterAndSort));
    document.getElementById('min').addEventListener('input', filterAndSort);
    document.getElementById('max').addEventListener('input', filterAndSort);
    document.getElementById('sort-select').addEventListener('change', filterAndSort);

    // NEW: Instant Search Listener
    const searchBar = document.querySelector('.search-bar');
    if (searchBar) {
        searchBar.addEventListener('input', filterAndSort);
    }

    // NEW: Check URL for search term (e.g. products.html?search=iphone)
    const params = new URLSearchParams(window.location.search);
    const urlSearch = params.get('search');
    if (urlSearch && searchBar) {
        searchBar.value = urlSearch;
        filterAndSort(); // Run immediately
    } else {
        render(products);
    }
}

// 3. LOGIC FOR DETAILS PAGE
const detailContainer = document.getElementById('detail-container');
if (detailContainer) {
    const params = new URLSearchParams(window.location.search);
    const id = parseInt(params.get('id'));
    const product = products.find(p => p.id === id);

    if (product) {

        detailContainer.innerHTML = `
            <div class="details-img">
                <img src="${product.image}" alt="${product.name}" style="mix-blend-mode: multiply;">
            </div>
            
            <div class="details-info">
                <span style="background: var(--primary-orange); color: white; padding: 4px 10px; border-radius: 4px; font-size: 12px;">${product.condition}</span>
                <h1 style="margin: 15px 0;">${product.name}</h1>
                <h2 style="color: var(--primary-orange); margin-bottom: 20px;">৳ ${product.price.toLocaleString()}</h2>
                
                <p><strong>Category:</strong> ${product.category}</p>
                <p style="margin-top: 10px;">${product.desc}</p>
                
                <div style="margin-top: 30px;">
                    <button class="btn-buy">Buy Now</button>
                </div>
            </div>
        `;
    } else {
        detailContainer.innerHTML = '<h2>Product not found! <a href="products.html">Go Back</a></h2>';
    }
}