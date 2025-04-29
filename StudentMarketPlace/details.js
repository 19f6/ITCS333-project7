document.addEventListener("DOMContentLoaded", () => { 
  const productTitle = document.querySelector(".title");
  const productCategory = document.querySelector(".group-detail-subject");
  const productDescription = document.querySelector(".description-section");
  const productImage = document.querySelector(".image");
  const commentsSection = document.querySelector(".comments-section");
  const commentsCount = document.querySelector(".comments-count");
  const commentForm = document.querySelector(".comment-form form");
  const commentTextarea = commentForm.querySelector("textarea");
  const backLink = document.querySelector(".back-link");

  const urlParams = new URLSearchParams(window.location.search);
  const productId = urlParams.get("id");
  const productTitleParam = urlParams.get("title");
  const productPrice = urlParams.get("price");
  const productImageParam = urlParams.get("image");
  const productDescParam = urlParams.get("description");

  if (productTitleParam) {
    productTitle.textContent = productTitleParam;
  }

  if (productImageParam) {
    productImage.src = productImageParam;
    productImage.alt = productTitleParam || "Product Image";
  }

  const generateContactInfo = () => {
    const email = `seller${Math.floor(1000 + Math.random() * 9000)}@unihub.edu`;
    const phone = `+973${Math.floor(35000000 + Math.random() * 50000000)}`;
    return {
      email,
      phone
    };
  };

  if (backLink) {
    backLink.addEventListener("click", (e) => {
      e.preventDefault();
      window.history.back();
    });
  }

  if (productDescParam || productId) {
    const contact = generateContactInfo();
    const conditions = ["New", "Like New", "Good", "Fair"];
    const randomCondition = conditions[Math.floor(Math.random() * conditions.length)];
    const randomRating = (Math.random() * 0.5 + 4.5).toFixed(1);
    
    productDescription.innerHTML = `
      <h1>Description</h1>
      <h2> ${productTitleParam || "Product"}  ${randomCondition.toUpperCase()}</h2>
      <p>${productDescParam || "This product is in great condition and perfect for students."}</p>

      <p><strong>Condition:</strong> ${randomCondition}  ${ 
        randomCondition === "New" ? "Never used" : 
        randomCondition === "Like New" ? "Minimal signs of use" :
        randomCondition === "Good" ? "Some highlighting/notes" :
        "Visible wear but fully functional"
      }</p>

      <p><strong>Key Features:</strong></p>
      <ul>
        <li>✅ Comprehensive coverage of core concepts</li>
        <li>✅ ${Math.floor(Math.random() * 500 + 500)} practice problems with solutions</li>
        <li>✅ Perfect for exam preparation and coursework</li>
        <li>✅ Clear explanations and examples</li>
      </ul>

      <p><strong> Student Rating:</strong> ${"★".repeat(5)} ${randomRating}/5</p>
      <p><em>"${[
        "Extremely helpful for my studies!",
        "Saved me so much time understanding difficult concepts.",
        "Condition was better than expected!",
        "Great value for the price."
      ][Math.floor(Math.random() * 4)]}"  Previous buyer</em></p>

      <p><strong> Price:</strong> <span style="color:#7c7fd9;">${productPrice || "N/A"}BD</span></p>

      <p><strong> Contact Seller:</strong><br>
        Email: <a href="mailto:${contact.email}">${contact.email}</a><br>
        Mobile: <a href="tel:${contact.phone}">${contact.phone}</a>
      </p>
      <p class="safety-tip">🔒 Meet in public campus locations for exchanges</p>
    `;
  }

  let comments = [];

  renderComments();

  function renderComments() {
    commentsCount.textContent = `Comments (${comments.length})`;
    commentsSection.insertAdjacentHTML("afterbegin", `
      ${comments.map(comment => `
        <div class="comment">
          <div class="comment-content">
            <div class="comment-header">
              <span class="comment-author">${comment.name}</span>
              <span class="comment-time">${comment.time}</span>
            </div>
            <p class="comment-text">${comment.text}</p>
          </div>
        </div>
      `).join('')}
    `);
  }

  commentForm.addEventListener("submit", (e) => {
    e.preventDefault();
    const commentText = commentTextarea.value.trim();
    
    if (!commentText) {
      alert("Please write a comment before submitting.");
      return;
    }

    const newComment = {
      name: "You",
      text: commentText,
      time: "Just now"
    };
    
    comments.unshift(newComment);
    renderComments();
    commentTextarea.value = "";
    
    const successMsg = document.createElement("div");
    successMsg.className = "comment-success";
    successMsg.textContent = "Comment posted!";
    commentForm.appendChild(successMsg);
    setTimeout(() => successMsg.remove(), 2000);
  });
});