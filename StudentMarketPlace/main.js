document.addEventListener("DOMContentLoaded", () => {
  const productList = document.querySelector(".product-list");
  const searchBox = document.querySelector(".search-box input");
  const categoryFilter = document.querySelector("#category-filter");
  const priceFilter = document.querySelector("#price-filter");
  const conditionFilter = document.querySelector("#condition-filter");
  const paginationContainer = document.querySelector(".pagination");
  const formToggle = document.getElementById("form-toggle");
  const formContainer = document.querySelector(".form-container");
  const closeForm = document.getElementById("close-form");
  const productForm = document.querySelector(".form-container form");

  let allProducts = [];
  let filteredProducts = [];
  let currentPage = 1;
  const productsPerPage = 8;

  const sampleProducts = [];

  function init() {
    allProducts = sampleProducts;
    filteredProducts = [...allProducts];
    renderProducts();
    setupPagination();
    setupEventListeners();
  }

  function setupEventListeners() {
    searchBox.addEventListener("input", debounce(filterAndSearch, 300));
    categoryFilter.addEventListener("change", filterAndSearch);
    priceFilter.addEventListener("change", filterAndSearch);
    conditionFilter.addEventListener("change", filterAndSearch);
    formToggle.addEventListener("click", () => {
      formContainer.classList.remove("hidden");
      formToggle.classList.add("hidden");
    });
    closeForm.addEventListener("click", () => {
      formContainer.classList.add("hidden");
      formToggle.classList.remove("hidden");
    });
    productForm.addEventListener("submit", handleFormSubmit);
  }

  function renderProducts() {
    productList.innerHTML = "";
    const start = (currentPage - 1) * productsPerPage;
    const end = start + productsPerPage;
    const paginatedProducts = filteredProducts.slice(start, end);
    if (paginatedProducts.length === 0) {
      productList.innerHTML = `
        <div class="empty-state">
          <p>No products found matching your criteria.</p>
        </div>
      `;
      return;
    }
    paginatedProducts.forEach((product) => {
      const stars = "★".repeat(product.rating) + "☆".repeat(5 - product.rating);
      const productHTML = `
        <div class="product-item">
          <img src="${product.image}" alt="${product.title}" class="product-image" />
          <div class="product-details">
            <h4>${product.title}</h4>
            <p>${product.description}</p>
            <p class="price">Price: ${product.price}BD</p>
            <div class="rating">
              ${stars} <span>(${product.reviews})</span>
            </div>
          </div>
          <a href="${product.link}?${new URLSearchParams({
            id: product.id,
            title: product.title,
            price: product.price,
            image: product.image,
            description: product.description
          }).toString()}" class="group-links">
            <button class="card-button">View Details</button>
          </a>
        </div>
      `;
      productList.insertAdjacentHTML("beforeend", productHTML);
    });
  }

  function setupPagination() {
    paginationContainer.innerHTML = "";
    const totalPages = Math.ceil(filteredProducts.length / productsPerPage);
    const prevLink = document.createElement("a");
    prevLink.href = "#";
    prevLink.innerHTML = "&laquo;";
    prevLink.className = currentPage === 1 ? "disabled" : "";
    prevLink.addEventListener("click", (e) => {
      e.preventDefault();
      if (currentPage > 1) {
        currentPage--;
        renderProducts();
        setupPagination();
      }
    });
    paginationContainer.appendChild(prevLink);
    for (let i = 1; i <= totalPages; i++) {
      const pageLink = document.createElement("a");
      pageLink.href = "#";
      pageLink.textContent = i;
      pageLink.className = i === currentPage ? "active" : "";
      pageLink.addEventListener("click", (e) => {
        e.preventDefault();
        currentPage = i;
        renderProducts();
        setupPagination();
      });
      paginationContainer.appendChild(pageLink);
    }
    const nextLink = document.createElement("a");
    nextLink.href = "#";
    nextLink.innerHTML = "&raquo;";
    nextLink.className = currentPage === totalPages ? "disabled" : "";
    nextLink.addEventListener("click", (e) => {
      e.preventDefault();
      if (currentPage < totalPages) {
        currentPage++;
        renderProducts();
        setupPagination();
      }
    });
    paginationContainer.appendChild(nextLink);
  }

  function filterAndSearch() {
    const searchQuery = searchBox.value.toLowerCase();
    const selectedCategory = categoryFilter.value;
    const selectedPrice = priceFilter.value;
    const selectedCondition = conditionFilter.value;
    filteredProducts = allProducts.filter((product) => {
      const matchesSearch = product.title.toLowerCase().includes(searchQuery) || 
                          product.description.toLowerCase().includes(searchQuery);
      const matchesCategory = selectedCategory === "Category" || 
                            product.category === selectedCategory;
      const matchesPrice = selectedPrice === "Price Range" || 
                         checkPriceRange(product.price, selectedPrice);
      const matchesCondition = selectedCondition === "Condition" || 
                             product.condition === selectedCondition;
      return matchesSearch && matchesCategory && matchesPrice && matchesCondition;
    });
    currentPage = 1;
    renderProducts();
    setupPagination();
  }

  function checkPriceRange(price, range) {
    const priceNum = parseFloat(price);
    switch (range) {
      case "$0 - $50": return priceNum <= 50;
      case "$50 - $100": return priceNum > 50 && priceNum <= 100;
      case "$100 - $200": return priceNum > 100 && priceNum <= 200;
      case "$200+": return priceNum > 200;
      default: return true;
    }
  }

  function handleFormSubmit(e) {
    e.preventDefault();
    const newProduct = {
      id: allProducts.length + 1,
      title: document.getElementById("product").value,
      description: document.getElementById("description").value,
      price: parseFloat(document.getElementById("price").value),
      image: "images/default-product.jpg",
      category: "General",
      condition: "New",
      rating: 0,
      reviews: 0,
      link: "details.html"
    };
    allProducts.unshift(newProduct);
    filteredProducts = [...allProducts];
    productForm.reset();
    formContainer.classList.add("hidden");
    formToggle.classList.remove("hidden");
    currentPage = 1;
    renderProducts();
    setupPagination();
  }

  function debounce(func, delay) {
    let timeoutId;
    return function() {
      const context = this;
      const args = arguments;
      clearTimeout(timeoutId);
      timeoutId = setTimeout(() => func.apply(context, args), delay);
    };
  }

  init();
});
