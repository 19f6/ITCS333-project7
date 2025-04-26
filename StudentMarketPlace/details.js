document.addEventListener("DOMContentLoaded", () => {
  const productTitle = document.querySelector(".title");
  const productCategory = document.querySelector(".group-detail-subject");
  const productDescription = document.querySelector(".description-section");
  const productImage = document.querySelector(".image");
  const commentsSection = document.querySelector(".comments-section");
  const commentsCount = document.querySelector(".comments-count");
  const commentForm = document.querySelector(".comment-form form");
  const commentTextarea = commentForm.querySelector("textarea");

  // Get product ID and price from URL
  const urlParams = new URLSearchParams(window.location.search);
  const productId = urlParams.get("id");
  const productPrice = urlParams.get("price");

  if (!productId) {
    alert("No product ID found in the URL.");
    return;
  }

  // Fetch product details
  fetch(`https://jsonplaceholder.typicode.com/posts/${productId}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Failed to fetch product details");
      }
      return response.json();
    })
    .then((product) => {
      // Generate dynamic email and contact
      const email = `contact+${productId}@unihub.com`;
      const contact = `+97335${Math.floor(100000 + Math.random() * 900000)}`;

      // Populate product details
      productTitle.textContent = product.title;
      productCategory.textContent = ["Math", "Art", "Science", "Technology"][
        Math.floor(Math.random() * 4)
      ];
      productDescription.innerHTML = `
        <h1>Description</h1>
        <p>${product.body}</p>
        <p><strong>💰 Price:</strong> <span style="color:#7c7fd9;">${productPrice}BD</span></p>
        <p><strong>📞 Contact:</strong><br>
          Email: <a href="mailto:${email}">${email}</a><br>
          Mobile: <a href="tel:${contact}">${contact}</a>
        </p>`;
      productImage.src = `https://picsum.photos/seed/${productId}/500/300`; // Consistent image URL
    })
    .catch((error) => {
      console.error(error);
      productDescription.innerHTML = "<p>Failed to load product details.</p>";
    });

  // Fetch comments
  fetch(`https://jsonplaceholder.typicode.com/comments?postId=${productId}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Failed to fetch comments");
      }
      return response.json();
    })
    .then((comments) => {
      commentsCount.textContent = `Comments (${comments.length})`;
      comments.forEach((comment) => {
        // Generate a random timestamp for existing comments
        const randomDate = new Date(Date.now() - Math.floor(Math.random() * 10000000000));
        const timeString = randomDate.toLocaleDateString([], { year: 'numeric', month: 'short', day: 'numeric' });

        const commentHTML = `
          <div class="comment">
            <div class="comment-header">
              <span class="comment-author">${comment.name}</span>
              <span class="comment-time">${timeString}</span>
            </div>
            <p>${comment.body}</p>
          </div>`;
        commentsSection.insertAdjacentHTML("beforeend", commentHTML);
      });
    })
    .catch((error) => {
      console.error(error);
      commentsSection.innerHTML = "<p>Failed to load comments.</p>";
    });

  // Add a new comment
  commentForm.addEventListener("submit", (e) => {
    e.preventDefault();
    const newComment = commentTextarea.value.trim();

    if (!newComment) {
      alert("Please write a comment before submitting.");
      return;
    }

    // Get the current time
    const now = new Date();
    const timeString = now.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });

    // Simulate adding a new comment
    const commentHTML = `
      <div class="comment">
        <div class="comment-header">
          <span class="comment-author">You</span>
          <span class="comment-time">${timeString}</span>
        </div>
        <p>${newComment}</p>
      </div>`;
    commentsSection.insertAdjacentHTML("beforeend", commentHTML);

    // Update comment count
    const currentCount = parseInt(commentsCount.textContent.match(/\d+/)[0]);
    commentsCount.textContent = `Comments (${currentCount + 1})`;

    // Clear the textarea
    commentTextarea.value = "";
  });
});
