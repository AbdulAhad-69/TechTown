// 1. THE DATA SET (Hardcoded Products Only)
const products = [
    // --- SMARTPHONES ---
    {
        id: 1,
        name: "iPhone 13 Pro Max",
        category: "Smartphones", price: 67000, condition: "Used - Like New",
        image: "assets/images/Apple-iPhone-13-Pro-Max.jpg",
        desc: "Battery Health 98%. Comes with box and cable.",
        specs: { "Display": "6.7-inch OLED", "Processor": "A15 Bionic", "RAM": "6GB", "Storage": "128GB", "Battery": "98% Health" }
    },
    {
        id: 2,
        name: "Samsung S23 Ultra",
        category: "Smartphones", price: 85500, condition: "Used - Good",
        image: "assets/images/Samsung-Galaxy-S23-Ultra.jpg",
        desc: "Korean variant. Minor scratches on bezel.",
        specs: { "Display": "6.8-inch AMOLED", "Processor": "Snapdragon 8 Gen 2", "RAM": "12GB", "Storage": "256GB", "Camera": "200MP" }
    },
    {
        id: 3,
        name: "Google Pixel 7 Pro",
        category: "Smartphones", price: 55000, condition: "Used - Fair",
        image: "assets/images/Google-Pixel-7-Pro.jpg",
        desc: "Device only. No issues with camera.",
        specs: { "Display": "6.7-inch OLED", "Processor": "Tensor G2", "RAM": "12GB", "Storage": "128GB" }
    },
    {
        id: 4,
        name: "OnePlus 11 5G",
        category: "Smartphones", price: 52000, condition: "Used - Like New",
        image: "assets/images/OnePlus-11-5G.jpg",
        desc: "Full box available. 12/256GB variant.",
        specs: { "Display": "6.7-inch AMOLED", "Processor": "Snapdragon 8 Gen 2", "RAM": "12GB", "Storage": "256GB", "Charging": "100W" }
    },
    {
        id: 5,
        name: "Xiaomi 13 Ultra",
        category: "Smartphones", price: 80000, condition: "Used - Good",
        image: "assets/images/Xiaomi-13-Ultra.jpg",
        desc: "Camera beast. Minor usage signs.",
        specs: { "Display": "6.73-inch AMOLED", "Processor": "Snapdragon 8 Gen 2", "RAM": "12GB", "Storage": "512GB", "Camera": "Leica Lens" }
    },

    // --- LAPTOPS ---
    {
        id: 6,
        name: "MacBook Air M2",
        category: "Laptops", price: 91000, condition: "New",
        image: "assets/images/MacBook-Air-M2.jpg",
        desc: "Brand new sealed pack. 1 Year Apple Warranty.",
        specs: { "Display": "13.6-inch Retina", "Processor": "M2 Chip", "RAM": "8GB", "Storage": "256GB SSD", "OS": "macOS" }
    },
    {
        id: 7,
        name: "Dell XPS 13",
        category: "Laptops", price: 215000, condition: "New",
        image: "assets/images/Dell-XPS-13.jpg",
        desc: "Latest gen, OLED screen.",
        specs: { "Display": "13.4-inch OLED", "Processor": "i7-1260P", "RAM": "16GB", "Storage": "512GB SSD", "OS": "Windows 11" }
    },
    {
        id: 8,
        name: "HP Spectre x360",
        category: "Laptops", price: 140000, condition: "New",
        image: "assets/images/HP-Spectre-x360.jpg",
        desc: "Convertible laptop with pen included.",
        specs: { "Display": "13.5-inch OLED", "Processor": "i7-1355U", "RAM": "16GB", "Storage": "1TB SSD", "Touch": "Yes" }
    },
    {
        id: 9,
        name: "Lenovo ThinkPad X1",
        category: "Laptops", price: 165000, condition: "New",
        image: "assets/images/Lenovo-ThinkPad-X1.jpg",
        desc: "Business class durability.",
        specs: { "Display": "14-inch IPS", "Processor": "i7 vPro", "RAM": "32GB", "Storage": "1TB SSD", "Weight": "1.12 kg" }
    },
    {
        id: 10,
        name: "Asus ZenBook Pro Duo 15",
        category: "Laptops", price: 318000, condition: "New",
        image: "assets/images/Asus-ZenBook-Pro-Duo-15.jpg",
        desc: "Dual screen laptop with pen support.",
        specs: { "Display": "15.6-inch OLED + 12-inch Touch", "Processor": "i7-13700H", "RAM": "32GB", "Storage": "1TB SSD", "Graphics": "RTX 4060" }
    },

    // --- CAMERAS ---
    {
        id: 11,
        name: "Sony Alpha a6400",
        category: "Cameras", price: 78000, condition: "Used - Like New",
        image: "assets/images/Sony-Alpha-a6400.jpg",
        desc: "Shutter count 5k. Comes with 16-50mm kit lens.",
        specs: { "Sensor": "24.2MP APS-C", "Video": "4K 30fps", "Focus": "Eye AF", "Lens": "Sony E-mount" }
    },
    {
        id: 12,
        name: "Canon EOS R50",
        category: "Cameras", price: 82000, condition: "New",
        image: "assets/images/Canon-EOS-R50.jpg",
        desc: "Brand new body only. Official warranty available.",
        specs: { "Sensor": "24.2MP APS-C", "Processor": "DIGIC X", "Video": "4K 30p", "Screen": "Vari-angle Touch" }
    },

    // --- SMART WATCHES ---
    {
        id: 13,
        name: "Apple Watch Ultra",
        category: "Smart Watches", price: 65000, condition: "Used - Good",
        image: "assets/images/Apple-Watch-Ultra.jpg",
        desc: "Battery health 100%. Minor scratch on casing.",
        specs: { "Case": "49mm Titanium", "Display": "Retina", "Water Resistance": "100m", "Connectivity": "GPS + Cellular" }
    },
    {
        id: 14,
        name: "Samsung Galaxy Watch 6",
        category: "Smart Watches", price: 28000, condition: "New",
        image: "assets/images/Samsung-Galaxy-Watch-6.jpg",
        desc: "Sealed box. Classic edition 47mm.",
        specs: { "Case": "47mm Steel", "Display": "Super AMOLED", "Health": "BIA Sensor", "OS": "Wear OS 4" }
    }
];

// 2. LOGIC FOR SHOP PAGE (products.html)
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

        // FILTER LOGIC
        let filtered = products.filter(p => {
            const matchesSearch = p.name.toLowerCase().includes(searchQuery) ||
                (p.desc && p.desc.toLowerCase().includes(searchQuery));
            return matchesSearch &&
                (cats.length === 0 || cats.includes(p.category)) &&
                (conds.length === 0 || conds.includes(p.condition)) &&
                (p.price >= min && p.price <= max);
        });

        if (sortValue === 'low-high') filtered.sort((a, b) => a.price - b.price);
        else if (sortValue === 'high-low') filtered.sort((a, b) => b.price - a.price);

        render(filtered);
    }

    // Event Listeners
    document.querySelectorAll('input').forEach(i => i.addEventListener('change', filterAndSort));
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) sortSelect.addEventListener('change', filterAndSort);

    const searchBar = document.querySelector('.search-bar');
    if (searchBar) searchBar.addEventListener('input', filterAndSort);

    // Initial Load
    const params = new URLSearchParams(window.location.search);
    const urlSearch = params.get('search');
    const urlCategory = params.get('category'); // 1. Get Category from URL

    if (urlSearch && searchBar) {
        searchBar.value = urlSearch;
        filterAndSort();
    } else if (urlCategory) {
        // 2. If Category found, check the sidebar box & filter
        const checkboxes = document.querySelectorAll('.filter-cat');
        checkboxes.forEach(cb => {
            if (cb.value === urlCategory) {
                cb.checked = true;
            }
        });
        filterAndSort();
    } else {
        render(products);
    }
}

// 3. LOGIC FOR DETAILS PAGE (product-details.html)
const detailContainer = document.getElementById('detail-container');
if (detailContainer) {
    const params = new URLSearchParams(window.location.search);
    const id = parseInt(params.get('id'));

    const product = products.find(p => p.id === id);

    if (product) {
        let specsHTML = '';
        if (product.specs) {
            const rows = Object.entries(product.specs).map(([key, value]) => `
                <tr><th>${key}</th><td>${value}</td></tr>
            `).join('');

            specsHTML = `
                <div class="specs-container">
                    <h3>Technical Specifications</h3>
                    <table class="specs-table"><tbody>${rows}</tbody></table>
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
                    <button class="btn-buy" onclick="addToCart(${product.id})" style="background-color: var(--primary-orange); color: white; border: none; padding: 15px 40px; font-size: 16px; font-weight: bold; border-radius: 6px; cursor: pointer;">Buy Now</button>
                    <button class="btn-chat" style="border: 1px solid var(--secondary-dark); background:white; color: var(--secondary-dark); padding: 15px 20px; font-size: 16px; font-weight: bold; border-radius: 6px; cursor: pointer; margin-left: 10px;">Chat with Seller</button>
                </div>
            </div>
            ${specsHTML}
        `;
    } else {
        detailContainer.innerHTML = '<h2>Product not found! <a href="products.html">Go Back</a></h2>';
    }
}

// 4. GLOBAL USER ICON LOGIC
document.addEventListener('DOMContentLoaded', function () {
    const user = JSON.parse(localStorage.getItem('currentUser'));
    const userIcon = document.querySelector('a.fa-user') || document.querySelector('.fa-user')?.parentElement;
    const directUserLink = document.querySelector('a.fa-user');

    updateCartCount();

    if (user) {
        if (userIcon) {
            userIcon.href = 'dashboard.html';
            userIcon.title = `Logged in as ${user.name}`;
        }
        if (directUserLink) {
            directUserLink.href = 'dashboard.html';
            directUserLink.title = `Logged in as ${user.name}`;
        }
    }

    // 7. HOMEPAGE LOGIC (Dynamic Index)
    const homeSmartphones = document.getElementById('home-smartphones');
    const homeLaptops = document.getElementById('home-laptops');

    if (homeSmartphones || homeLaptops) {
        function renderHomeCategory(container, category, limit) {
            if (!container) return;
            const filtered = products.filter(p => p.category === category).slice(0, limit);

            if (filtered.length === 0) {
                container.innerHTML = `<p>No items found.</p>`;
                return;
            }

            container.innerHTML = filtered.map(p => `
                <a href="product-details.html?id=${p.id}" class="product-card" style="text-decoration:none; color:inherit;">
                    <div class="product-img">
                        <img src="${p.image}" alt="${p.name}">
                    </div>
                    <div class="product-info">
                        <h3>${p.name}</h3>
                        <div class="product-meta">Starting from</div>
                        <div class="product-price">৳ ${p.price.toLocaleString()}</div>
                    </div>
                </a>
            `).join('');
        }

        renderHomeCategory(homeSmartphones, 'Smartphones', 5);
        renderHomeCategory(homeLaptops, 'Laptops', 5);
    }
});

// 5. SHOPPING CART LOGIC
function addToCart(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;

    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const existingItem = cart.find(item => item.id === productId);

    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            image: product.image,
            quantity: 1
        });
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    alert(`${product.name} added to cart!`);
    updateCartCount();
}

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const countBadge = document.getElementById('cart-count');
    if (countBadge) countBadge.innerText = `(${totalItems})`;
}

// Render Cart Page (cart.html)
if (document.getElementById('cartTable')) {
    function renderCart() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const tbody = document.getElementById('cart-body');
        const emptyMsg = document.getElementById('empty-cart-msg');

        tbody.innerHTML = '';
        let subtotal = 0;

        if (cart.length === 0) {
            emptyMsg.style.display = 'block';
        } else {
            emptyMsg.style.display = 'none';
            cart.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                tbody.innerHTML += `
                    <tr>
                        <td>
                            <div class="cart-product-info">
                                <img src="${item.image}" alt="${item.name}">
                                <div>
                                    <h4 style="font-size:14px; margin-bottom:4px;">${item.name}</h4>
                                    <small>ID: ${item.id}</small>
                                </div>
                            </div>
                        </td>
                        <td>৳ ${item.price.toLocaleString()}</td>
                        <td>
                            <input type="number" class="qty-input" value="${item.quantity}" min="1" onchange="updateQty(${index}, this.value)">
                        </td>
                        <td>৳ ${itemTotal.toLocaleString()}</td>
                        <td><button class="remove-btn" onclick="removeItem(${index})"><i class="fas fa-trash"></i></button></td>
                    </tr>
                `;
            });
        }
        document.getElementById('sub-total').innerText = '৳ ' + subtotal.toLocaleString();
        document.getElementById('final-total').innerText = '৳ ' + (subtotal + 120).toLocaleString();
    }

    window.updateQty = function (index, newQty) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        if (newQty < 1) newQty = 1;
        cart[index].quantity = parseInt(newQty);
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();
        updateCartCount();
    };

    window.removeItem = function (index) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart.splice(index, 1);
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();
        updateCartCount();
    };

    renderCart();
}

// 6. CHECKOUT LOGIC (checkout.html)
if (document.getElementById('checkout-items')) {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const itemList = document.getElementById('checkout-items');
    let subtotal = 0;

    if (cart.length === 0) {
        alert("Your cart is empty!");
        window.location.href = "products.html";
    } else {
        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            subtotal += itemTotal;
            itemList.innerHTML += `
                <div class="checkout-item-row">
                    <span>${item.quantity}x ${item.name}</span>
                    <span>৳ ${itemTotal.toLocaleString()}</span>
                </div>
            `;
        });
        document.getElementById('checkout-subtotal').innerText = '৳ ' + subtotal.toLocaleString();
        document.getElementById('checkout-total').innerText = '৳ ' + (subtotal + 120).toLocaleString();
    }
}

const checkoutForm = document.getElementById('checkoutForm');
if (checkoutForm) {
    checkoutForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const user = JSON.parse(localStorage.getItem('currentUser'));
        if (!user) {
            alert("Please login to complete your order.");
            window.location.href = "login.html";
            return;
        }

        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const total = parseInt(document.getElementById('checkout-total').innerText.replace(/[^\d]/g, ''));

        const newOrder = {
            id: 'ORD-' + Date.now(),
            date: new Date().toLocaleDateString(),
            items: cart,
            total: total,
            status: 'Pending',
            userEmail: user.email,
            shipping: {
                name: document.getElementById('shipName').value,
                phone: document.getElementById('shipPhone').value,
                city: document.getElementById('shipCity').value,
                address: document.getElementById('shipAddress').value
            }
        };

        let allOrders = JSON.parse(localStorage.getItem('allOrders')) || [];
        allOrders.push(newOrder);
        localStorage.setItem('allOrders', JSON.stringify(allOrders));

        localStorage.removeItem('cart');

        alert("Order Placed Successfully! Order ID: " + newOrder.id);
        window.location.href = "dashboard.html";
    });
}