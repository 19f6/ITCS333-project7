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

  
  const sampleProducts = [
    {
      "id": 1,
      "title": "Calculus 1",
      "description": "A great book to explain the basics of calculus.",
      "price": 22,
      "rating": 4,
      "reviews": 20,
      "image": "images/Calculus Book 1.jpg",
      "link": "details.html",
      "category": "Math",
      "condition": "New"
    },
    {
      "id": 2,
      "title": "Introduction to Algorithms",
      "description": "A comprehensive update of the leading algorithms text.",
      "price": 30,
      "rating": 3,
      "reviews": 55,
      "image": "images/Introduction to Algorithms by Thomas H. Cormen.jpg",
      "link": "details.html",
      "category": "Technology",
      "condition": "Used - Like New"
    },
    {
      "id": 3,
      "title": "Artificial Intelligence: A Modern Approach",
      "description": "A detailed look into AI concepts.",
      "price": 45,
      "rating": 4,
      "reviews": 79,
      "image": "images/artificialintelligence-Amodernapproach.jpg",
      "link": "details.html",
      "category": "Technology",
      "condition": "New"
    },
    {
      "id": 4,
      "title": "Database Systems: The Complete Book",
      "description": "Comprehensive coverage of database design.",
      "price": 35,
      "rating": 3,
      "reviews": 65,
      "image": "images/DatabaseSystems-TheCompleteBook.jpg",
      "link": "details.html",
      "category": "Technology",
      "condition": "Used - Good"
    },
    {
      "id": 5,
      "title": "Operating System Concepts",
      "description": "Learn the core principles of modern operating systems.",
      "price": 38,
      "rating": 5,
      "reviews": 25,
      "image": "images/OperatingSystemConcepts.jpg",
      "link": "details.html",
      "category": "Technology",
      "condition": "New"
    },
    {
      "id": 6,
      "title": "Computer Networking",
      "description": "Discover how the internet and networking protocols function.",
      "price": 40,
      "rating": 5,
      "reviews": 52,
      "image": "images/ComputerNetworking-ATop-DownApproach.jpg",
      "link": "details.html",
      "category": "Technology",
      "condition": "Used - Acceptable"
    },
    {
      "id": 7,
      "title": "Introduction to Machine Learning",
      "description": "An essential guide for beginners in machine learning.",
      "price": 50,
      "rating": 3,
      "reviews": 300,
      "image": "images/IntroductiontoMachineLearning.jpg",
      "link": "details.html",
      "category": "Science",
      "condition": "Used - Like New"
    },
    {
      "id": 8,
      "title": "Software Engineering",
      "description": "Everything about software development methodologies.",
      "price": 42,
      "rating": 4,
      "reviews": 45,
      "image": "images/SoftwareEngineering-APractitioner'sApproach.jpg",
      "link": "details.html",
      "category": "Technology",
      "condition": "New"
    },
    {
      "id": 9,
      "title": "Linear Algebra",
      "description": "Includes real-world case studies.",
      "price": 46,
      "rating": 3,
      "reviews": 220,
      "image": "images/LinearAlgebra.jpg",
      "link": "details.html",
      "category": "Engineering",
      "condition": "New"
    },
    {
      "id": 10,
      "title": "Discrete Mathematics",
      "description": "Well-suited for self-study and classroom use.",
      "price": 44,
      "rating": 4,
      "reviews": 244,
      "image": "images/DiscreteMathematics.jpg",
      "link": "details.html",
      "category": "Math",
      "condition": "New"
    },
    {
      "id": 11,
      "title": "Modern Physics",
      "description": "Includes real-world case studies.",
      "price": 58,
      "rating": 3,
      "reviews": 280,
      "image": "images/ModernPhysics.jpg",
      "link": "details.html",
      "category": "Math",
      "condition": "New"
    },
    {
      "id": 12,
      "title": "Advanced Calculus",
      "description": "Covers both theory and practical examples.",
      "price": 47,
      "rating": 5,
      "reviews": 89,
      "image": "images/AdvancedCalculus.jpg",
      "link": "details.html",
      "category": "Math",
      "condition": "Used - Like New"
    },
    {
      "id": 13,
      "title": "Web Development Bootcamp",
      "description": "Well-suited for self-study and classroom use.",
      "price": 26,
      "rating": 4,
      "reviews": 262,
      "image": "images/WebDevelopmentBootcamp.jpg",
      "link": "details.html",
      "category": "Engineering",
      "condition": "Used - Good"
    },
    {
      "id": 14,
      "title": "Data Science Essentials",
      "description": "Covers both theory and practical examples.",
      "price": 27,
      "rating": 5,
      "reviews": 195,
      "image": "images/DataScienceEssentials.jpg",
      "link": "details.html",
      "category": "Technology",
      "condition": "Used - Acceptable"
    },
    {
      "id": 15,
      "title": "Cloud Computing",
      "description": "Covers both theory and practical examples.",
      "price": 22,
      "rating": 3,
      "reviews": 74,
      "image": "images/CloudComputing.jpg",
      "link": "details.html",
      "category": "Math",
      "condition": "New"
    },
    {
      "id": 16,
      "title": "Cryptography Basics",
      "description": "Covers both theory and practical examples.",
      "price": 48,
      "rating": 3,
      "reviews": 282,
      "image": "images/CryptographyBasics.jpg",
      "link": "details.html",
      "category": "Math",
      "condition": "New"
    },
    {
      "id": 17,
      "title": "Cybersecurity Principles",
      "description": "A detailed introduction to the subject.",
      "price": 34,
      "rating": 5,
      "reviews": 71,
      "image": "images/CybersecurityPrinciples.jpg",
      "link": "details.html",
      "category": "Math",
      "condition": "Used - Acceptable"
    },
    {
      "id": 18,
      "title": "Agile Project Management",
      "description": "Covers both theory and practical examples.",
      "price": 52,
      "rating": 3,
      "reviews": 40,
      "image": "images/AgileProjectManagement.jpg",
      "link": "details.html",
      "category": "Engineering",
      "condition": "Used - Acceptable"
    },
    {
      "id": 19,
      "title": "Quantum Computing",
      "description": "Covers both theory and practical examples.",
      "price": 39,
      "rating": 4,
      "reviews": 256,
      "image": "images/QuantumComputing.jpg",
      "link": "details.html",
      "category": "Engineering",
      "condition": "New"
    },
    {
      "id": 20,
      "title": "Big Data Analytics",
      "description": "Covers both theory and practical examples.",
      "price": 55,
      "rating": 3,
      "reviews": 204,
      "image": "images/BigDataAnalytics.jpg",
      "link": "details.html",
      "category": "Math",
      "condition": "Used - Good"
    },
    {
      "id": 21,
      "title": "Mobile App Development",
      "description": "Covers both theory and practical examples.",
      "price": 22,
      "rating": 3,
      "reviews": 258,
      "image": "images/MobileAppDevelopment.jpg",
      "link": "details.html",
      "category": "Engineering",
      "condition": "Used - Like New"
    },
    {
      "id": 22,
      "title": "UX/UI Design",
      "description": "Includes real-world case studies.",
      "price": 28,
      "rating": 3,
      "reviews": 97,
      "image": "images/UX/UIDesign.jpg",
      "link": "details.html",
      "category": "Science",
      "condition": "Used - Like New"
    },
    {
      "id": 23,
      "title": "Human-Computer Interaction",
      "description": "A detailed introduction to the subject.",
      "price": 49,
      "rating": 5,
      "reviews": 133,
      "image": "images/Human-ComputerInteraction.jpg",
      "link": "details.html",
      "category": "Technology",
      "condition": "Used - Like New"
    },
    {
      "id": 24,
      "title": "Functional Programming",
      "description": "Includes real-world case studies.",
      "price": 31,
      "rating": 4,
      "reviews": 100,
      "image": "images/FunctionalProgramming.jpg",
      "link": "details.html",
      "category": "Math",
      "condition": "Used - Acceptable"
    },
    {
      "id": 25,
      "title": "Compiler Design",
      "description": "A detailed introduction to the subject.",
      "price": 28,
      "rating": 4,
      "reviews": 132,
      "image": "images/CompilerDesign.jpg",
      "link": "details.html",
      "category": "Technology",
      "condition": "New"
    },
    {
      "id": 26,
      "title": "Bioinformatics",
      "description": "Includes real-world case studies.",
      "price": 59,
      "rating": 4,
      "reviews": 70,
      "image": "images/Bioinformatics.jpg",
      "link": "details.html",
      "category": "Technology",
      "condition": "New"
    },
    {
      "id": 27,
      "title": "Robotics Fundamentals",
      "description": "Well-suited for self-study and classroom use.",
      "price": 35,
      "rating": 4,
      "reviews": 55,
      "image": "images/RoboticsFundamentals.jpg",
      "link": "details.html",
      "category": "Math",
      "condition": "Used - Acceptable"
    },
    {
      "id": 28,
      "title": "Deep Learning with Python",
      "description": "Well-suited for self-study and classroom use.",
      "price": 57,
      "rating": 5,
      "reviews": 101,
      "image": "images/DeepLearningwithPython.jpg",
      "link": "details.html",
      "category": "Engineering",
      "condition": "Used - Like New"
    }
  ];

  
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