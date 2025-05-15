document.addEventListener("DOMContentLoaded", () => {
  const productList = document.querySelector(".product-list");
  const searchBox = document.querySelector(".search-box input");
  const categoryFilter = document.querySelector("#category-filter");
  const priceFilter = document.querySelector("#price-filter");
  const paginationContainer = document.querySelector(".pagination");
  const formToggle = document.getElementById("form-toggle");
  const formContainer = document.querySelector(".form-container");
  const closeForm = document.getElementById("close-form");
  const productForm = document.querySelector(".form-container form");

  let allProducts = [];
  let filteredProducts = [];
  let currentPage = 1;
  const productsPerPage = 8;

  async function fetchProducts() {
    try {
      const response = await fetch("api/items.php");
      if (!response.ok) throw new Error("Network response was not ok");
      return await response.json();
    } catch (error) {
      console.error("Error fetching products:", error);
      return [];
    }
  }

  function showErrorToast(message) {
    const toast = document.createElement("div");
    toast.className = "error-toast";
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
  }

  async function init() {
    allProducts = await fetchProducts();
    filteredProducts = [...allProducts];

    renderProducts();
    setupPagination();
    setupEventListeners();
  }

  function setupEventListeners() {
    searchBox.addEventListener("input", debounce(filterAndSearch, 300));
    categoryFilter.addEventListener("change", filterAndSearch);
    priceFilter.addEventListener("change", filterAndSearch);

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
      const stars = "★".repeat(product.rating || 0) + "☆".repeat(5 - (product.rating || 0));
      const productHTML = `
        <div class="product-item">
          <img src="${product.image}" alt="${product.title}" class="product-image" />
          <div class="product-details">
            <h4>${product.title}</h4>
            <p>${product.description}</p>
            <p class="price">Price: ${product.price}BD</p>
            <div class="rating">
              ${stars} <span>(${product.reviews || 0})</span>
            </div>
          </div>
          <a href="details.php?id=${product.id}" class="group-links">
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

  async function filterAndSearch() {
    const searchQuery = searchBox.value.toLowerCase();
    const selectedCategory = categoryFilter.value;
    const selectedPrice = priceFilter.value;

    try {
        let url = "api/items.php?";
        const params = [];

        if (searchQuery) params.push(`search=${encodeURIComponent(searchQuery)}`);
        if (selectedCategory && selectedCategory !== "Category") {
            params.push(`category=${encodeURIComponent(selectedCategory)}`);
        }
        if (selectedPrice !== "Price Range") {
            const priceRange = getPriceRange(selectedPrice);
            params.push(`price_range=${priceRange.min}-${priceRange.max}`);
        }

        const response = await fetch(url + params.join("&"));
        if (!response.ok) throw new Error("Network response was not ok");
        filteredProducts = await response.json();

        currentPage = 1;
        renderProducts();
        setupPagination();
    } catch (error) {
        console.error("Error filtering products:", error);
        showErrorToast("Failed to filter products. Please try again.");
    }
}

  function getPriceRange(priceText) {
    switch (priceText) {
      case "$0 - $50":
        return { min: 0, max: 50 };
      case "$50 - $100":
        return { min: 50, max: 100 };
      case "$100 - $200":
        return { min: 100, max: 200 };
      case "$200+":
        return { min: 200, max: 9999 };
      default:
        return { min: 0, max: 9999 };
    }
  }

  async function handleFormSubmit(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append("title", document.getElementById("product").value);
    formData.append("description", document.getElementById("description").value);
    formData.append("price", document.getElementById("price").value);
    formData.append("image", document.getElementById("image").files[0]);
    formData.append("category", document.getElementById("category").value);

    try {
      const response = await fetch("api/add_product.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();
      if (!response.ok) {
        console.error("Backend error:", result);
        alert(result.error || "Failed to add product. Please try again.");
        return;
      }

      window.location.href = `details.php?id=${result.id}`;
    } catch (error) {
      console.error("Error:", error);
      alert("Failed to add product. Please try again.");
    }
  }

  function debounce(func, delay) {
    let timeoutId;
    return function () {
      const context = this;
      const args = arguments;
      clearTimeout(timeoutId);
      timeoutId = setTimeout(() => func.apply(context, args), delay);
    };
  }

  init();
});
