<?php
require __DIR__.'/includes/config.php';
require __DIR__.'/includes/functions.php';
require_login();

$errors = [];
$success = null;

$devices = [
  'iPhone', 'Samsung Galaxy', 'Google Pixel', 'OnePlus', 'Xiaomi',
  'iPad', 'MacBook', 'Dell Laptop', 'HP Laptop', 'Lenovo Laptop',
  'AirPods', 'Headphones', 'Speakers', 'Smart Watch', 'Tablet'
];

$commonIssues = [
  'Screen Replacement', 'Battery Replacement', 'Charging Port Repair',
  'Speaker/Microphone Repair', 'Camera Repair', 'Water Damage',
  'Software Issues', 'Button Repair', 'Other'
];

$repairServices = [
  [
    'id' => 1,
    'name' => "Screen Replacement",
    'devices' => ["iPhone", "Samsung Galaxy", "Google Pixel", "OnePlus", "iPad"],
    'price' => "€89 - €299",
    'duration' => "1-2 hours",
    'warranty' => "6 months",
    'description' => "High-quality screen replacement with original parts"
  ],
  [
    'id' => 2,
    'name' => "Battery Replacement",
    'devices' => ["iPhone", "Samsung Galaxy", "Google Pixel", "OnePlus", "MacBook"],
    'price' => "€49 - €129",
    'duration' => "30-45 minutes",
    'warranty' => "12 months",
    'description' => "Genuine battery replacement for optimal performance"
  ],
  [
    'id' => 3,
    'name' => "Charging Port Repair",
    'devices' => ["iPhone", "Samsung Galaxy", "Google Pixel", "OnePlus"],
    'price' => "€39 - €89",
    'duration' => "45-60 minutes",
    'warranty' => "6 months",
    'description' => "Fix charging issues with professional repair"
  ],
  [
    'id' => 4,
    'name' => "Water Damage Recovery",
    'devices' => ["iPhone", "Samsung Galaxy", "Google Pixel", "OnePlus", "iPad"],
    'price' => "€79 - €199",
    'duration' => "2-4 hours",
    'warranty' => "3 months",
    'description' => "Advanced water damage repair and data recovery"
  ],
  [
    'id' => 5,
    'name' => "Software Diagnostics",
    'devices' => ["All Devices"],
    'price' => "€29",
    'duration' => "30 minutes",
    'warranty' => "N/A",
    'description' => "Complete software diagnosis and optimization"
  ]
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    check_csrf();
    $device = trim($_POST['device'] ?? '');
    $issue = trim($_POST['issue'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $contact_info = trim($_POST['contact_info'] ?? '');
    $urgency = trim($_POST['urgency'] ?? 'normal');

    if ($device === '' || $issue === '' || $description === '' || $contact_info === '') {
      $errors[] = 'Please fill in all required fields.';
    }

    $p1 = $p2 = $p3 = null;
    if (!$errors) {
      $p1 = handle_image_upload($_FILES['picture1'] ?? [], false);
      $p2 = handle_image_upload($_FILES['picture2'] ?? [], false);
      $p3 = handle_image_upload($_FILES['picture3'] ?? [], false);

      $st = $pdo->prepare("INSERT INTO repair_requests (user_id, device, issue, description, contact_info, urgency, picture1, picture2, picture3) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $st->execute([
        $_SESSION['user']['id'], $device, $issue, $description, $contact_info, $urgency, $p1, $p2, $p3
      ]);
      $ticket_id = $pdo->lastInsertId();
      $success = "Repair request submitted! Ticket #" . $ticket_id;

      // Notify admin via email (if mail function is available)
      $admin_email = 'admin@imobile.com'; // Change this to actual admin email
      $subject = "New Repair Request - Ticket #$ticket_id";
      $message = "A new repair request has been submitted:\n\n" .
                 "Ticket ID: $ticket_id\n" .
                 "User ID: {$_SESSION['user']['id']}\n" .
                 "Device: $device\n" .
                 "Issue: $issue\n" .
                 "Urgency: $urgency\n" .
                 "Contact: $contact_info\n" .
                 "Description: $description\n\n" .
                 "Please review this request in the admin panel.";

      $headers = "From: noreply@imobile.com\r\n" .
                 "Reply-To: $contact_info\r\n" .
                 "X-Mailer: PHP/" . phpversion();

      // Try to send email (won't work in local environment without mail server)
      @mail($admin_email, $subject, $message, $headers);
    }
  } catch (Throwable $e) {
    $errors[] = $e->getMessage();
  }
}

require __DIR__.'/includes/header.php';
?>

<div class="repair-page">
  <header class="header">
    <nav class="nav">
      <div class="nav-tabs">
        <a href="index.php" class="tab">All Categories</a>
        <a href="repair.php" class="tab active">🔧 Repair Services</a>
      </div>
      <a href="cart.php" class="cart">0 items - €0</a>
    </nav>
  </header>

  <main class="main">
    <!-- Hero Section -->
    <div class="repair-hero">
      <div class="hero-content">
        <h1>EXPERT DEVICE REPAIR CENTER</h1>
        <p class="hero-subtitle">Certified technicians • Genuine parts • Fast turnaround • Extended warranty</p>
        <div class="hero-stats">
          <div class="stat">
            <div class="stat-number">25,000+</div>
            <div class="stat-label">Repairs Completed</div>
          </div>
          <div class="stat">
            <div class="stat-number">99.5%</div>
            <div class="stat-label">Customer Satisfaction</div>
          </div>
          <div class="stat">
            <div class="stat-number">2hrs</div>
            <div class="stat-label">Average Repair Time</div>
          </div>
        </div>
      </div>
      <div class="hero-image">
        <div class="floating-devices">
          <div class="device-icon">📱</div>
          <div class="device-icon">💻</div>
          <div class="device-icon">⌚</div>
          <div class="device-icon">🎧</div>
        </div>
      </div>
    </div>

    <!-- Emergency Banner -->
    <div class="emergency-banner">
      <div class="emergency-content">
        <div class="emergency-icon">🚨</div>
        <div class="emergency-text">
          <h3>URGENT REPAIR NEEDED?</h3>
          <p>Broken screen? Won't charge? Dead battery? Get same-day service!</p>
        </div>
        <button class="emergency-btn">📞 CALL NOW: +1 (555) FIX-NOW</button>
      </div>
    </div>

    <!-- Services Grid -->
    <div class="repair-services">
      <div class="section-header">
        <h2>EXPERT REPAIR SERVICES</h2>
        <p>Professional repairs with genuine parts and lifetime warranty</p>
      </div>
      <div class="services-grid">
        <?php foreach ($repairServices as $service): ?>
          <div class="service-card premium">
            <div class="service-badge">PREMIUM</div>
            <div class="service-header">
              <h3><?php echo htmlspecialchars($service['name']); ?></h3>
              <div class="service-price"><?php echo htmlspecialchars($service['price']); ?></div>
            </div>
            <div class="service-details">
              <p class="service-description"><?php echo htmlspecialchars($service['description']); ?></p>
              <div class="service-features">
                <div class="feature">
                  <span class="feature-icon">⏱️</span>
                  <span><?php echo htmlspecialchars($service['duration']); ?></span>
                </div>
                <div class="feature">
                  <span class="feature-icon">🛡️</span>
                  <span><?php echo htmlspecialchars($service['warranty']); ?> warranty</span>
                </div>
              </div>
              <div class="service-devices">
                <strong>Compatible Devices:</strong> <?php echo htmlspecialchars(implode(', ', $service['devices'])); ?>
              </div>
            </div>
            <button class="service-btn premium-btn" onclick="scrollToForm()">GET INSTANT QUOTE</button>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Repair Request Form -->
    <div id="repair-form" class="repair-form-section premium-form">
      <div class="form-header">
        <h2>REQUEST PROFESSIONAL REPAIR</h2>
        <p>Get your device fixed by certified technicians</p>
      </div>

      <?php if ($success): ?>
        <p class="note" style="color:green"><?php echo htmlspecialchars($success); ?></p>
      <?php endif; ?>
      <?php if ($errors): ?>
        <ul class="note" style="color:#e53935"><?php foreach ($errors as $e) { echo "<li>" . htmlspecialchars($e) . "</li>"; } ?></ul>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data" data-validate>
        <?php csrf_field(); ?>
        <div class="form-row">
          <div class="form-group">
            <label for="device">Device Model *</label>
            <select id="device" name="device" required class="premium-select">
              <option value="">Choose your device</option>
              <?php foreach ($devices as $device): ?>
                <option value="<?php echo htmlspecialchars($device); ?>" <?php echo (isset($_POST['device']) && $_POST['device'] === $device) ? 'selected' : ''; ?>><?php echo htmlspecialchars($device); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="issue">Repair Type *</label>
            <select id="issue" name="issue" required class="premium-select">
              <option value="">Select repair type</option>
              <?php foreach ($commonIssues as $issue): ?>
                <option value="<?php echo htmlspecialchars($issue); ?>" <?php echo (isset($_POST['issue']) && $_POST['issue'] === $issue) ? 'selected' : ''; ?>><?php echo htmlspecialchars($issue); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="description">Detailed Problem Description *</label>
          <textarea id="description" name="description" required placeholder="Describe the issue, when it started, any error messages..." rows="5" class="premium-textarea"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="contact_info">Contact Number *</label>
            <input type="tel" id="contact_info" name="contact_info" required placeholder="+1 (555) 123-4567" value="<?php echo htmlspecialchars($_POST['contact_info'] ?? ''); ?>" class="premium-input">
          </div>

          <div class="form-group">
            <label for="urgency">Urgency Level</label>
            <select id="urgency" name="urgency" class="premium-select">
              <option value="normal" <?php echo (!isset($_POST['urgency']) || $_POST['urgency'] === 'normal') ? 'selected' : ''; ?>>Normal (3-5 days)</option>
              <option value="urgent" <?php echo (isset($_POST['urgency']) && $_POST['urgency'] === 'urgent') ? 'selected' : ''; ?>>Urgent (24-48 hours)</option>
              <option value="emergency" <?php echo (isset($_POST['urgency']) && $_POST['urgency'] === 'emergency') ? 'selected' : ''; ?>>Emergency (Same day)</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Picture 1</label>
            <input type="file" name="picture1" accept="image/*">
          </div>
          <div class="form-group">
            <label>Picture 2</label>
            <input type="file" name="picture2" accept="image/*">
          </div>
          <div class="form-group">
            <label>Picture 3</label>
            <input type="file" name="picture3" accept="image/*">
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="submit-repair-btn premium-submit">
            🚀 SUBMIT REPAIR REQUEST
          </button>
          <p class="form-note">* Free diagnostic • No obligation • Response within 1 hour</p>
        </div>
      </form>
    </div>

    <!-- Why Choose Us - Aggressive -->
    <div class="repair-benefits premium-benefits">
      <div class="section-header">
        <h2>WHY CHOOSE iMOBILE REPAIRS?</h2>
        <p>Leading mobile repair service with certified technicians and genuine parts</p>
      </div>
      <div class="benefits-grid premium-grid">
        <div class="benefit-item premium-item">
          <div class="benefit-icon">🏆</div>
          <h3>CERTIFIED EXPERTS</h3>
          <p>Apple & Samsung certified technicians • Factory-trained specialists • 10+ years experience</p>
        </div>
        <div class="benefit-item premium-item">
          <div class="benefit-icon">⚡</div>
          <h3>FASTEST SERVICE</h3>
          <p>Most repairs in 1-2 hours • Same-day service available • Express pickup & delivery</p>
        </div>
        <div class="benefit-item premium-item">
          <div class="benefit-icon">💎</div>
          <h3>GENUINE PARTS</h3>
          <p>100% OEM parts • Manufacturer warranty • Superior quality guarantee</p>
        </div>
        <div class="benefit-item premium-item">
          <div class="benefit-icon">🛡️</div>
          <h3>EXTENDED WARRANTY</h3>
          <p>6-12 months warranty on repairs • 100% satisfaction guarantee • Free diagnostics</p>
        </div>
        <div class="benefit-item premium-item">
          <div class="benefit-icon">💰</div>
          <h3>COMPETITIVE PRICING</h3>
          <p>Best prices in the market • Transparent pricing • No hidden fees • Money-back guarantee</p>
        </div>
        <div class="benefit-item premium-item">
          <div class="benefit-icon">🎯</div>
          <h3>ALL DEVICES</h3>
          <p>iPhone, Samsung, Google Pixel, OnePlus, Xiaomi, iPad, MacBook, and more</p>
        </div>
      </div>
    </div>

    <!-- Testimonials -->
    <div class="testimonials-section">
      <div class="section-header">
        <h2>WHAT OUR CUSTOMERS SAY</h2>
        <p>Real reviews from satisfied customers</p>
      </div>
      <div class="testimonials-grid">
        <div class="testimonial-card">
          <div class="testimonial-stars">★★★★★</div>
          <p>"Fixed my iPhone screen in 30 minutes! Professional service and great price."</p>
          <div class="testimonial-author">- Sarah Johnson, CEO</div>
        </div>
        <div class="testimonial-card">
          <div class="testimonial-stars">★★★★★</div>
          <p>"Water damaged my laptop, they recovered all data and fixed it perfectly!"</p>
          <div class="testimonial-author">- Mike Chen, Developer</div>
        </div>
        <div class="testimonial-card">
          <div class="testimonial-stars">★★★★★</div>
          <p>"Best repair service in the city. Genuine parts and lifetime warranty."</p>
          <div class="testimonial-author">- Emily Davis, Designer</div>
        </div>
      </div>
    </div>
  </main>
</div>

<?php include __DIR__.'/includes/footer.php'; ?>