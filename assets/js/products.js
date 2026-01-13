// 1. THE DATA SET
const products = [
    // --- SMARTPHONES ---
    {
        id: 1,
        name: "iPhone 13 Pro Max",
        category: "Smartphones", price: 67000, condition: "Used - Like New",
        image: "assets/images/Apple-iPhone-13-Pro-Max.jpg",
        desc: "Battery Health 98%. Comes with box and cable.",
        specs: {
            "Display": "6.7-inch Super Retina XDR OLED",
            "Processor": "A15 Bionic chip",
            "RAM": "6GB",
            "Storage": "128GB",
            "Battery": "4352 mAh (98% Health)",
            "Camera": "12MP Pro Triple System"
        }
    },
    {
        id: 2,
        name: "Samsung S23 Ultra",
        category: "Smartphones", price: 85500, condition: "Used - Good",
        image: "assets/images/Samsung-Galaxy-S23-Ultra.jpg",
        desc: "Korean variant. Minor scratches on bezel.",
        specs: {
            "Display": "6.8-inch Dynamic AMOLED 2X",
            "Processor": "Snapdragon 8 Gen 2",
            "RAM": "12GB",
            "Storage": "256GB",
            "Battery": "5000 mAh",
            "Camera": "200MP Quad Camera"
        }
    },
    {
        id: 3,
        name: "Google Pixel 7 Pro",
        category: "Smartphones", price: 55000, condition: "Used - Fair",
        image: "assets/images/Google-Pixel-7-Pro.jpg",
        desc: "Device only. No issues with camera.",
        specs: {
            "Display": "6.7-inch LTPO OLED",
            "Processor": "Google Tensor G2",
            "RAM": "12GB",
            "Storage": "128GB",
            "Battery": "5000 mAh",
            "Camera": "50MP Triple Camera"
        }
    },
    {
        id: 4,
        name: "OnePlus 11 5G",
        category: "Smartphones", price: 52000, condition: "Used - Like New",
        image: "assets/images/OnePlus-11-5G.jpg",
        desc: "Full box available. 12/256GB variant.",
        specs: {
            "Display": "6.7-inch AMOLED 120Hz",
            "Processor": "Snapdragon 8 Gen 2",
            "RAM": "12GB",
            "Storage": "256GB",
            "Battery": "5000 mAh",
            "Charging": "100W SuperVOOC"
        }
    },
    {
        id: 5,
        name: "Xiaomi 13 Ultra",
        category: "Smartphones", price: 80000, condition: "Used - Good",
        image: "assets/images/Xiaomi-13-Ultra.jpg",
        desc: "Camera beast. Minor usage signs.",
        specs: {
            "Display": "6.73-inch WQHD+ AMOLED",
            "Processor": "Snapdragon 8 Gen 2",
            "RAM": "12GB",
            "Storage": "512GB",
            "Camera": "50MP Leica Quad Lens",
            "Battery": "5000 mAh"
        }
    },

    // --- LAPTOPS ---
    {
        id: 6,
        name: "MacBook Air M2",
        category: "Laptops", price: 91000, condition: "New",
        image: "assets/images/MacBook-Air-M2.jpg",
        desc: "Brand new sealed pack. 1 Year Apple Warranty.",
        specs: {
            "Display": "13.6-inch Liquid Retina",
            "Processor": "Apple M2 Chip (8-core CPU)",
            "RAM": "8GB Unified Memory",
            "Storage": "256GB SSD",
            "Battery": "Up to 18 hours",
            "OS": "macOS Sonoma"
        }
    },
    {
        id: 7,
        name: "Dell XPS 13",
        category: "Laptops", price: 215000, condition: "New",
        image: "assets/images/Dell-XPS-13.jpg",
        desc: "Latest gen, OLED screen.",
        specs: {
            "Display": "13.4-inch OLED Touch",
            "Processor": "Intel Core i7-1260P",
            "RAM": "16GB LPDDR5",
            "Storage": "512GB NVMe SSD",
            "Graphics": "Intel Iris Xe",
            "OS": "Windows 11 Home"
        }
    },
    {
        id: 8,
        name: "HP Spectre x360",
        category: "Laptops", price: 140000, condition: "New",
        image: "assets/images/HP-Spectre-x360.jpg",
        desc: "Convertible laptop with pen included.",
        specs: {
            "Display": "13.5-inch 3K2K OLED",
            "Processor": "Intel Core i7-1355U",
            "RAM": "16GB LPDDR4x",
            "Storage": "1TB SSD",
            "Touch": "Yes (Pen Included)",
            "Audio": "Bang & Olufsen"
        }
    },
    {
        id: 9,
        name: "Lenovo ThinkPad X1",
        category: "Laptops", price: 165000, condition: "New",
        image: "assets/images/Lenovo-ThinkPad-X1.jpg",
        desc: "Business class durability.",
        specs: {
            "Display": "14-inch IPS Anti-glare",
            "Processor": "Intel Core i7 vPro",
            "RAM": "32GB Soldered",
            "Storage": "1TB SSD",
            "Build": "Carbon Fiber Hybrid",
            "Weight": "1.12 kg"
        }
    },

    // --- CAMERAS ---
    {
        id: 10,
        name: "Sony Alpha a6400",
        category: "Cameras", price: 78000, condition: "Used - Like New",
        image: "assets/images/Sony-Alpha-a6400.jpg",
        desc: "Shutter count 5k. Comes with 16-50mm kit lens.",
        specs: {
            "Sensor": "24.2MP APS-C Exmor CMOS",
            "ISO Range": "100-32000",
            "Video": "4K UHD at 30fps",
            "Autofocus": "Real-time Eye AF",
            "Lens Mount": "Sony E-mount",
            "Connectivity": "Wi-Fi, NFC, Bluetooth"
        }
    },
    {
        id: 11,
        name: "Canon EOS R50",
        category: "Cameras", price: 82000, condition: "New",
        image: "assets/images/Canon-EOS-R50.jpg",
        desc: "Brand new body only. Official warranty available.",
        specs: {
            "Sensor": "24.2MP APS-C CMOS",
            "Processor": "DIGIC X",
            "Video": "4K uncropped 30p",
            "Screen": "Vari-angle Touchscreen",
            "Lens Mount": "Canon RF Mount",
            "Weight": "375g (Body)"
        }
    },

    // --- SMART WATCHES ---
    {
        id: 12,
        name: "Apple Watch Ultra",
        category: "Smart Watches", price: 65000, condition: "Used - Good",
        image: "assets/images/Apple-Watch-Ultra.jpg",
        desc: "Battery health 100%. Minor scratch on casing.",
        specs: {
            "Case": "49mm Titanium",
            "Display": "Always-On Retina (2000 nits)",
            "Water Resistance": "100m (WR100)",
            "Battery": "Up to 36 hours",
            "Sensors": "Blood Oxygen, ECG, Temp",
            "Connectivity": "GPS + Cellular"
        }
    },
    {
        id: 13,
        name: "Samsung Galaxy Watch 6",
        category: "Smart Watches", price: 28000, condition: "New",
        image: "assets/images/Samsung-Galaxy-Watch-6.jpg",
        desc: "Sealed box. Classic edition 47mm.",
        specs: {
            "Case": "47mm Stainless Steel",
            "Display": "Super AMOLED Sapphire",
            "Bezel": "Rotating Bezel",
            "Health": "Sleep Coaching, BIA Sensor",
            "OS": "Wear OS 4",
            "Battery": "425 mAh"
        }
    }
];

// 2. LOGIC FOR SHOP PAGE
const grid = document.getElementById('product-grid');

if (grid) {
    function render(items) {
        if (items.length === 0) {
            grid.innerHTML = '<p style="text-align:center; grid-column:1/-1;">No products found.</p>';
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
        const searchInput = document.querySelector('.search-bar');
        const searchQuery = searchInput ? searchInput.value.toLowerCase() : '';
        const cats = [...document.querySelectorAll('.filter-cat:checked')].map(c => c.value);
        const conds = [...document.querySelectorAll('.filter-cond:checked')].map(c => c.value);
        const min = parseInt(document.getElementById('min').value) || 0;
        const max = parseInt(document.getElementById('max').value) || 9999999;
        const sortValue = document.getElementById('sort-select').value;

        let filtered = products.filter(p => {
            const matchesSearch = p.name.toLowerCase().includes(searchQuery) ||
                p.desc.toLowerCase().includes(searchQuery);
            return matchesSearch &&
                (cats.length === 0 || cats.includes(p.category)) &&
                (conds.length === 0 || conds.includes(p.condition)) &&
                (p.price >= min && p.price <= max);
        });

        if (sortValue === 'low-high') filtered.sort((a, b) => a.price - b.price);
        else if (sortValue === 'high-low') filtered.sort((a, b) => b.price - a.price);

        render(filtered);
    }

    document.querySelectorAll('input').forEach(i => i.addEventListener('change', filterAndSort));
    document.getElementById('min').addEventListener('input', filterAndSort);
    document.getElementById('max').addEventListener('input', filterAndSort);
    document.getElementById('sort-select').addEventListener('change', filterAndSort);

    const searchBar = document.querySelector('.search-bar');
    if (searchBar) searchBar.addEventListener('input', filterAndSort);

    const params = new URLSearchParams(window.location.search);
    const urlSearch = params.get('search');
    if (urlSearch && searchBar) {
        searchBar.value = urlSearch;
        filterAndSort();
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
        // Generate Table Rows from Specs Object
        let specsHTML = '';
        if (product.specs) {
            const rows = Object.entries(product.specs).map(([key, value]) => `
                <tr>
                    <th>${key}</th>
                    <td>${value}</td>
                </tr>
            `).join('');

            specsHTML = `
                <div class="specs-container">
                    <h3>Technical Specifications</h3>
                    <table class="specs-table">
                        <tbody>${rows}</tbody>
                    </table>
                </div>
            `;
        }

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
                    <button class="btn-buy" style="background-color: var(--primary-orange); color: white; border: none; padding: 15px 40px; font-size: 16px; font-weight: bold; border-radius: 6px; cursor: pointer;">Buy Now</button>
                    <button class="btn-chat" style="border: 1px solid var(--secondary-dark); background:white; color: var(--secondary-dark); padding: 15px 20px; font-size: 16px; font-weight: bold; border-radius: 6px; cursor: pointer; margin-left: 10px;">Chat with Seller</button>
                </div>
            </div>

            ${specsHTML}
        `;
    } else {
        detailContainer.innerHTML = '<h2>Product not found! <a href="products.html">Go Back</a></h2>';
    }
}