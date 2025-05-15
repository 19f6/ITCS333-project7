document.addEventListener("DOMContentLoaded", () => {
  // First check if we're on a product details page
  const productDetailsContainer = document.querySelector(
    ".product-details-container"
  );
  if (!productDetailsContainer) {
    console.log("Not on product details page - skipping details.js");
    return;
  }

  // Safely get all DOM elements
  const getElement = (selector) => {
    const el = document.querySelector(selector);
    if (!el) console.warn(`Element not found: ${selector}`);
    return el;
  };

  const elements = {
    title: getElement(".title"),
    category: getElement(".group-detail-subject"),
    description: getElement(".description-section"),
    image: getElement(".image"),
    commentsSection: getElement(".comments-section"),
    commentsCount: getElement(".comments-count"),
    commentForm: getElement(".comment-form form"),
    backLink: getElement(".back-link"),
  };

  // Exit if essential elements are missing
  if (!elements.title || !elements.description || !elements.image) {
    console.error("Essential product detail elements are missing");
    return;
  }

  // Get URL parameters
  const urlParams = new URLSearchParams(window.location.search);
  const productData = {
    id: urlParams.get("id"),
    title: urlParams.get("title"),
    price: urlParams.get("price"),
    image: urlParams.get("image"),
    description: urlParams.get("description"),
  };

  // Set basic product info
  if (productData.title) elements.title.textContent = productData.title;
  if (productData.image) {
    elements.image.src = productData.image;
    elements.image.alt = productData.title || "Product Image";
  }

  // Generate random contact information
  const generateContactInfo = () => ({
    email: `seller${Math.floor(1000 + Math.random() * 9000)}@unihub.edu`,
    phone: `+973${Math.floor(35000000 + Math.random() * 50000000)}`,
  });

  // Back button functionality
  if (elements.backLink) {
    elements.backLink.addEventListener("click", (e) => {
      e.preventDefault();
      window.history.back();
    });
  }

  // Populate product description
  if (productData.description || productData.id) {
    const contact = generateContactInfo();
    const conditions = ["New", "Like New", "Good", "Fair"];
    const randomCondition =
      conditions[Math.floor(Math.random() * conditions.length)];
    const randomRating = (Math.random() * 0.5 + 4.5).toFixed(1);

    elements.description.innerHTML = `
      <h1>Description</h1>
      <h2>${
        productData.title || "Product"
      } ${randomCondition.toUpperCase()}</h2>
      <p>${
        productData.description ||
        "This product is in great condition and perfect for students."
      }</p>

      <p><strong>Condition:</strong> ${randomCondition} ${
      randomCondition === "New"
        ? "Never used"
        : randomCondition === "Like New"
        ? "Minimal signs of use"
        : randomCondition === "Good"
        ? "Some highlighting/notes"
        : "Visible wear but fully functional"
    }</p>

      <p><strong>Key Features:</strong></p>
      <ul>
        <li>✅ Comprehensive coverage of core concepts</li>
        <li>✅ ${Math.floor(
          Math.random() * 500 + 500
        )} practice problems with solutions</li>
        <li>✅ Perfect for exam preparation and coursework</li>
        <li>✅ Clear explanations and examples</li>
      </ul>

      <p><strong>Student Rating:</strong> ${"★".repeat(5)} ${randomRating}/5</p>
      <p><em>"${
        [
          "Extremely helpful for my studies!",
          "Saved me so much time understanding difficult concepts.",
          "Condition was better than expected!",
          "Great value for the price.",
        ][Math.floor(Math.random() * 4)]
      }" - Previous buyer</em></p>

      <p><strong>Price:</strong> <span style="color:#7c7fd9;">${
        productData.price || "N/A"
      }BD</span></p>

      <p><strong>Contact Seller:</strong><br>
        Email: <a href="mailto:${contact.email}">${contact.email}</a><br>
        Mobile: <a href="tel:${contact.phone}">${contact.phone}</a>
      </p>
      <p class="safety-tip">🔒 Meet in public campus locations for exchanges</p>
    `;
  }

  // Comments functionality
  let comments = [];

  function renderComments() {
    if (!elements.commentsSection || !elements.commentsCount) return;

    elements.commentsSection.innerHTML = "";
    elements.commentsCount.textContent = `Comments (${comments.length})`;

    comments.forEach((comment) => {
      elements.commentsSection.insertAdjacentHTML(
        "beforeend",
        `
        <div class="comment">
          <div class="comment-content">
            <div class="comment-header">
              <span class="comment-author">${comment.name}</span>
              <span class="comment-time">${comment.time}</span>
            </div>
            <p class="comment-text">${comment.text}</p>
          </div>
        </div>
      `
      );
    });
  }

  renderComments();

  // Comment form submission
  if (elements.commentForm) {
    const textarea = elements.commentForm.querySelector("textarea");
    if (textarea) {
      elements.commentForm.addEventListener("submit", (e) => {
        e.preventDefault();
        const commentText = textarea.value.trim();

        if (!commentText) {
          alert("Please write a comment before submitting.");
          return;
        }

        comments.unshift({
          name: "You",
          text: commentText,
          time: "Just now",
        });

        renderComments();
        textarea.value = "";

        const successMsg = document.createElement("div");
        successMsg.className = "comment-success";
        successMsg.textContent = "Comment posted!";
        elements.commentForm.appendChild(successMsg);
        setTimeout(() => successMsg.remove(), 2000);
      });
    }
  }
});

// Handle comment form submission
document.getElementById('commentForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const messageEl = document.getElementById('commentMessage');
    
    try {
        const response = await fetch('API/add_comment.php', {
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) {
            throw new Error(await response.text());
        }
        
        // Success - reload comments
        messageEl.textContent = "Comment posted successfully!";
        messageEl.style.color = 'green';
        messageEl.style.display = 'block';
        form.reset();
        
        // Refresh comments after a short delay
        setTimeout(() => {
            window.location.reload();
        }, 1000);
        
    } catch (error) {
        messageEl.textContent = "Error: " + error.message;
        messageEl.style.color = 'red';
        messageEl.style.display = 'block';
    }
});

// API-based initialization function
function initDetails(productData, commentsData = []) {
  const container = document.querySelector(".product-details-container");
  if (!container) return;

  const elements = {
    title: document.querySelector(".title"),
    description: document.querySelector(".description-section"),
    image: document.querySelector(".image"),
    commentsSection: document.querySelector(".comments-section"),
    commentsCount: document.querySelector(".comments-count"),
  };

  // Verify essential elements exist
  if (!elements.title || !elements.description || !elements.image) {
    console.error("Essential elements missing for initDetails");
    return;
  }

  // Set product info
  elements.title.textContent = productData.title;
  elements.image.src = productData.image;
  elements.image.alt = productData.title;

  elements.description.innerHTML = `
    <h1>Description</h1>
    <h2>${productData.title} - ${productData.item_condition.toUpperCase()}</h2>
    <p>${productData.description}</p>
    <p><strong>Condition:</strong> ${productData.item_condition}</p>
    <p><strong>Price:</strong> <span style="color:#7c7fd9;">${
      productData.price
    }BD</span></p>
    <p><strong>Contact Seller:</strong><br>
      Email: <a href="mailto:${productData.seller_email}">${
    productData.seller_email
  }</a>
    </p>
  `;

  // Render comments if available
  if (elements.commentsSection && elements.commentsCount) {
    elements.commentsCount.textContent = `Comments (${commentsData.length})`;
    elements.commentsSection.innerHTML = commentsData
      .map(
        (comment) => `
      <div class="comment">
        <div class="comment-content">
          <div class="comment-header">
            <span class="comment-author">${comment.author}</span>
            <span class="comment-time">${new Date(
              comment.created_at
            ).toLocaleDateString()}</span>
          </div>
          <p class="comment-text">${comment.comment_text}</p>
        </div>
      </div>
    `
      )
      .join("");
  }
}

window.initDetails = initDetails;
