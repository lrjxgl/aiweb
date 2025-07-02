// Global variables
let products = [
    { id: 1, name: "Product 1", description: "This is product 1", price: 19.99 },
    { id: 2, name: "Product 2", description: "This is product 2", price: 29.99 },
    { id: 3, name: "Product 3", description: "This is product 3", price: 39.99 }
];
let cart = [];

// DOM elements
const productList = document.getElementById('productList');
const cartItems = document.getElementById('cartItems');
const cartTotal = document.getElementById('cartTotal');
const checkoutBtn = document.getElementById('checkoutBtn');
const productsLink = document.getElementById('productsLink');
const cartLink = document.getElementById('cartLink');
const productsSection = document.getElementById('products');
const cartSection = document.getElementById('cart');

// Event listeners
productsLink.addEventListener('click', showProducts);
cartLink.addEventListener('click', showCart);
checkoutBtn.addEventListener('click', checkout);

// Display products
function displayProducts() {
    productList.innerHTML = '';
    products.forEach(product => {
        const productElement = document.createElement('div');
        productElement.classList.add('product');
        productElement.innerHTML = `
            <h3>${product.name}</h3>
            <p>${product.description}</p>
            <p>Price: $${product.price}</p>
            <button onclick="addToCart(${product.id})">Add to Cart</button>
        `;
        productList.appendChild(productElement);
    });
}

// Add product to cart
function addToCart(productId) {
    const product = products.find(p => p.id === productId);
    if (product) {
        const cartItem = cart.find(item => item.id === productId);
        if (cartItem) {
            cartItem.quantity++;
        } else {
            cart.push({ ...product, quantity: 1 });
        }
        updateCart();
    }
}

// Update cart display
function updateCart() {
    cartItems.innerHTML = '';
    let total = 0;
    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        const cartItemElement = document.createElement('div');
        cartItemElement.classList.add('cart-item');
        cartItemElement.innerHTML = `
            <span>${item.name} x ${item.quantity}</span>
            <span>$${itemTotal.toFixed(2)}</span>
        `;
        cartItems.appendChild(cartItemElement);
    });
    cartTotal.textContent = total.toFixed(2);
}

// Show products section
function showProducts(e) {
    e.preventDefault();
    productsSection.style.display = 'block';
    cartSection.style.display = 'none';
}

// Show cart section
function showCart(e) {
    e.preventDefault();
    productsSection.style.display = 'none';
    cartSection.style.display = 'block';
}

// Checkout
function checkout() {
    if (cart.length === 0) {
        alert('Your cart is empty!');
        return;
    }

    alert('Thank you for your purchase! This is a demo, so no actual order will be processed.');
    cart = [];
    updateCart();
}

// Initialize the app
displayProducts();