// Toggle mega menu
document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.getElementById('btnAllCategories');
  const menu = document.getElementById('megaMenu');
  if (toggle && menu) {
    toggle.addEventListener('click', (e) => {
      e.preventDefault();
      const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', !isExpanded);
      menu.hidden = isExpanded;
      if (!isExpanded) {
        window.scrollTo({top: 0, behavior: 'smooth'});
      }
    });
  }

  // Simple client-side validation hint
  document.querySelectorAll('form[data-validate]').forEach(form => {
    form.addEventListener('submit', () => {
      form.querySelectorAll('[required]').forEach(el => {
        if (!el.value.trim()) el.classList.add('invalid');
      });
    });
    form.querySelectorAll('input, textarea').forEach(el => {
      el.addEventListener('input', () => el.classList.remove('invalid'));
    });
  });

  // Function to render products (placeholder)
  window.renderProducts = function(container, products, title) {
    if (!products || products.length === 0) {
      container.innerHTML = '<p>No products available.</p>';
      return;
    }

    let html = `<h3>${title}</h3><div class="grid">`;
    products.forEach(product => {
      const originalPriceHtml = product.originalPrice ? `<p class="original-price">${product.originalPrice}</p>` : '';
      html += `
        <div class="product-card">
          <img src="${product.image}" alt="${product.name}" onerror="this.src='https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=300&h=300&fit=crop'">
          <h4>${product.name}</h4>
          ${originalPriceHtml}
          <p class="price">${product.price}</p>
          <button class="btn add-to-cart-btn" data-name="${product.name}" data-price="${product.price.replace('€', '')}" data-image="${product.image}">Add to Cart</button>
        </div>
      `;
    });
    html += '</div>';
    container.innerHTML = html;

    // Add event listeners for add to cart buttons
    container.querySelectorAll('.add-to-cart-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const name = this.getAttribute('data-name');
        const price = this.getAttribute('data-price');
        const image = this.getAttribute('data-image');

        addToCart(name, price, image);
      });
    });
  };

  // Function to add item to cart
  window.addToCart = function(productName, productPrice, productImage) {
    // Check if user is logged in by looking for logout link (indicates logged in)
    const logoutLink = document.querySelector('a[href*="logout"]');

    if (!logoutLink) {
      window.location.href = 'admin/login.php';
      return;
    }

    const formData = new FormData();
    formData.append('product_name', productName);
    formData.append('product_price', productPrice);
    formData.append('product_image', productImage);

    fetch('add_to_cart.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Update cart count in header
        const cartBadge = document.getElementById('cartCount');
        if (cartBadge) {
          cartBadge.textContent = data.cart_count;
        }
      }
      alert('Item successfully added to cart!');
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Item successfully added to cart!');
    });
  };

  // Function to update cart item quantity
  window.updateQuantity = function(itemId, newQty) {
    const formData = new FormData();
    formData.append('action', 'update_quantity');
    formData.append('item_id', itemId);
    formData.append('quantity', newQty);

    fetch('update_cart.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Reload page to show updated cart
        window.location.reload();
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error updating cart');
    });
  };

  // Function to remove cart item
  window.removeItem = function(itemId) {
    if (!confirm('Remove this item from cart?')) return;

    const formData = new FormData();
    formData.append('action', 'remove_item');
    formData.append('item_id', itemId);

    fetch('update_cart.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Reload page to show updated cart
        window.location.reload();
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error removing item');
    });
  };

  // Repair page specific functionality
  if (document.querySelector('.repair-page')) {
    // Function to scroll to repair form
    window.scrollToForm = function() {
      const form = document.getElementById('repair-form');
      if (form) {
        form.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
        // Focus on first form field
        const firstField = form.querySelector('select[required]');
        if (firstField) {
          setTimeout(() => firstField.focus(), 500);
        }
      }
    };

    // Service quote buttons
    document.querySelectorAll('.service-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const serviceName = this.closest('.service-card').querySelector('h3').textContent;
        // Scroll to form instead of alert
        scrollToForm();
        // Optionally pre-select the service type if possible
        const form = document.getElementById('repair-form');
        if (form) {
          const issueSelect = form.querySelector('select[name="issue"]');
          if (issueSelect) {
            // Try to match the service name to an option
            const options = issueSelect.querySelectorAll('option');
            options.forEach(option => {
              if (option.textContent.toLowerCase().includes(serviceName.toLowerCase().split(' ')[0])) {
                option.selected = true;
              }
            });
          }
        }
      });
    });

    // Emergency call button
    const emergencyBtn = document.querySelector('.emergency-btn');
    if (emergencyBtn) {
      emergencyBtn.addEventListener('click', function() {
        window.location.href = 'tel:+15551234567';
      });
    }

    // Form validation enhancement
    const repairForm = document.querySelector('.repair-form');
    if (repairForm) {
      repairForm.addEventListener('submit', function(e) {
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
          if (!field.value.trim()) {
            field.classList.add('invalid');
            isValid = false;
          } else {
            field.classList.remove('invalid');
          }
        });

        if (!isValid) {
          e.preventDefault();
          alert('Please fill in all required fields.');
        }
      });

      // Real-time validation
      repairForm.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('blur', function() {
          if (this.hasAttribute('required') && !this.value.trim()) {
            this.classList.add('invalid');
          } else {
            this.classList.remove('invalid');
          }
        });
      });
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });

    // Add loading state to form submission
    const submitBtn = document.querySelector('.submit-repair-btn');
    if (submitBtn) {
      repairForm.addEventListener('submit', function() {
        submitBtn.textContent = 'Submitting...';
        submitBtn.disabled = true;
      });
    }
  }
});