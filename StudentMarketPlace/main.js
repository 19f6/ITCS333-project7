document.addEventListener("DOMContentLoaded", () => {
  const productList = document.querySelector(".product-list");
  const searchBox = document.querySelector(".search-box input");
  const categoryFilter = document.querySelector("#category-filter");
  const priceFilter = document.querySelector("#price-filter");
  const conditionFilter = document.querySelector("#condition-filter");
  const paginationContainer = document.querySelector(".pagination");
  const loader = document.createElement("p");
  loader.textContent = "Loading products...";
  productList.appendChild(loader);

  let allProducts = [];
  let filteredProducts = [];
  let currentPage = 1;
  const productsPerPage = 6;

  // Fetch data from the mock API
  fetch("https://jsonplaceholder.typicode.com/posts")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Failed to fetch data");
      }
      return response.json();
    })
    .then((data) => {
      // Map API data to product structure
      allProducts = data.map((item) => ({
        id: item.id,
        title: item.title,
        description: item.body,
        price: Math.floor(Math.random() * 50 + 10), // Random price
        image: `https://picsum.photos/seed/${item.id}/300/200`, // Consistent image URL
        category: ["Math", "Art", "Science", "Technology"][
          Math.floor(Math.random() * 4)
        ],
        condition: [
          "New",
          "Used - Like New",
          "Used - Good",
          "Used - Acceptable",
        ][Math.floor(Math.random() * 4)],
      }));
      filteredProducts = allProducts;
      loader.remove();
      renderProducts();
      setupPagination();
    })
    .catch((error) => {
      loader.textContent = "Failed to load products. Please try again.";
      console.error(error);
    });

  // Render products dynamically
  function renderProducts() {
    productList.innerHTML = "";
    const start = (currentPage - 1) * productsPerPage;
    const end = start + productsPerPage;
    const paginatedProducts = filteredProducts.slice(start, end);

    if (paginatedProducts.length === 0) {
      productList.innerHTML = "<p>No products found.</p>";
      return;
    }

    paginatedProducts.forEach((product) => {
      const productHTML = `
        <div class="product-item">
          <img src="${product.image}" alt="${product.title}" class="product-image" />
          <div class="product-details">
            <h4>${product.title}</h4>
            <p>${product.description.substring(0, 50)}...</p>
            <p class="price">Price: ${product.price}BD</p>
          </div>
          <a href="details.html?id=${product.id}&price=${product.price}" class="group-links">
            <button class="card-button">View Details</button>
          </a>
        </div>`;
      productList.insertAdjacentHTML("beforeend", productHTML);
    });
  }

  // Setup pagination
  function setupPagination() {
    paginationContainer.innerHTML = "";
    const totalPages = Math.ceil(filteredProducts.length / productsPerPage);

    for (let i = 1; i <= totalPages; i++) {
      const pageLink = document.createElement("a");
      pageLink.textContent = i;
      pageLink.href = "#";
      if (i === currentPage) pageLink.classList.add("active");
      pageLink.addEventListener("click", (e) => {
        e.preventDefault();
        currentPage = i;
        renderProducts();
        setupPagination();
      });
      paginationContainer.appendChild(pageLink);
    }
  }

  // Filter and search functionality
  function filterAndSearch() {
    const searchQuery = searchBox.value.toLowerCase();
    const selectedCategory = categoryFilter.value;
    const selectedPrice = priceFilter.value;
    const selectedCondition = conditionFilter.value;

    filteredProducts = allProducts.filter((product) => {
      const matchesSearch = product.title.toLowerCase().includes(searchQuery);
      const matchesCategory =
        selectedCategory === "Category" || product.category === selectedCategory;
      const matchesPrice =
        selectedPrice === "Price Range" ||
        checkPriceRange(product.price, selectedPrice);
      const matchesCondition =
        selectedCondition === "Condition" ||
        product.condition === selectedCondition;

      return matchesSearch && matchesCategory && matchesPrice && matchesCondition;
    });

    currentPage = 1;
    renderProducts();
    setupPagination();
  }

  // Check price range
  function checkPriceRange(price, range) {
    switch (range) {
      case "$0 - $50":
        return price <= 50;
      case "$50 - $100":
        return price > 50 && price <= 100;
      case "$100 - $200":
        return price > 100 && price <= 200;
      case "$200+":
        return price > 200;
      default:
        return true;
    }
  }

  // Event listeners for filters and search
  searchBox.addEventListener("input", filterAndSearch);
  categoryFilter.addEventListener("change", filterAndSearch);
  priceFilter.addEventListener("change", filterAndSearch);
  conditionFilter.addEventListener("change", filterAndSearch);
});