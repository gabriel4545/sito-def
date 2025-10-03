
<!DOCTYPE html>
<html lang="it">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>GPIOS - Area Riservata</title>
	
	<!-- Favicon -->
	<link rel="icon" type="image/png" href="img/logo.png">
	<link rel="shortcut icon" type="image/png" href="img/logo.png">
	<link rel="apple-touch-icon" href="img/logo.png">
	
	<!-- Bootstrap CSS -->
	<link href="css/bootstrap.css" rel="stylesheet">
	
	<!-- Bootstrap Icons -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
	
	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	
	<!-- Login Page Styles -->
	<link href="css/login.css" rel="stylesheet">
</head>
<body>
	<!-- Back to Home Button -->
	<div class="back-home">
		<a href="index.php" class="btn">
			<i class="bi bi-arrow-left me-2"></i>Torna al Sito
		</a>
	</div>

	<!-- Login Container -->
	<div class="login-container">
		<!-- Header -->
		<div class="login-header">
			<div class="mb-4">
				<div class="floating-icon">
					<i class="bi bi-shield-lock-fill text-primary" style="font-size: 4rem;"></i>
				</div>
			</div>
			<h2 class="fw-bold text-dark mb-2">Area Riservata</h2>
			<p class="text-muted mb-0">Accedi alla dashboard di amministrazione</p>
		</div>

		<!-- Body -->
		<div class="login-body">
			<div id="login-alert" class="mb-4"></div>
			
			<form id="loginForm" autocomplete="off">
				<div class="mb-4">
					<label for="email" class="form-label fw-semibold text-dark">
						<i class="bi bi-envelope me-2 text-primary"></i>Indirizzo Email
					</label>
					<input type="email" 
						   class="form-control" 
						   id="email" 
						   name="email" 
						   placeholder="Inserisci la tua email"
						   required 
						   autofocus>
				</div>
				
				<div class="mb-4">
					<label for="password" class="form-label fw-semibold text-dark">
						<i class="bi bi-key me-2 text-primary"></i>Password
					</label>
					<div class="position-relative">
						<input type="password" 
							   class="form-control" 
							   id="password" 
							   name="password" 
							   placeholder="Inserisci la tua password"
							   required>
						<button type="button" 
								class="btn password-toggle position-absolute top-50 end-0 translate-middle-y me-3" 
								onclick="togglePassword()"
								id="toggleBtn">
							<i class="bi bi-eye" id="toggleIcon"></i>
						</button>
					</div>
				</div>
				
				<button type="submit" class="btn btn-login w-100 mb-4">
					<i class="bi bi-box-arrow-in-right me-2"></i>
					Accedi alla Dashboard
				</button>
			</form>
			
			<!-- Security Info -->
			<div class="text-center">
				<div class="security-info">
					<small class="text-success d-flex align-items-center justify-content-center fw-semibold">
						<i class="bi bi-shield-check me-2"></i>
						Connessione sicura e crittografata
					</small>
				</div>
			</div>
		</div>
	</div>
	<script src="js/bootstrap.bundle.js"></script>
	
	<script>
		// Toggle password 
		function togglePassword() {
			const passwordField = document.getElementById('password');
			const toggleIcon = document.getElementById('toggleIcon');
			
			if (passwordField.type === 'password') {
				passwordField.type = 'text';
				toggleIcon.className = 'bi bi-eye-slash';
			} else {
				passwordField.type = 'password';
				toggleIcon.className = 'bi bi-eye';
			}
		}

		// Form 
		document.getElementById('loginForm').addEventListener('submit', async function(e) {
			e.preventDefault();
			
			const email = document.getElementById('email').value;
			const password = document.getElementById('password').value;
			const alertBox = document.getElementById('login-alert');
			const submitBtn = e.target.querySelector('button[type="submit"]');
			
			// Reset alert
			alertBox.innerHTML = '';
			
			
			submitBtn.disabled = true;
			submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Autenticazione...';
			
			try {
				const response = await fetch('backend_login.php', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({ email, password })
				});
				
				const data = await response.json();
				
				if (data.success) {
					alertBox.innerHTML = `
						<div class='alert alert-success d-flex align-items-center'>
							<i class="bi bi-check-circle-fill me-2"></i>
							<div>
								<strong>Accesso effettuato!</strong><br>
								<small>${data.message}</small>
							</div>
						</div>
					`;
					
					// Success 
					submitBtn.innerHTML = '<i class="bi bi-check2 me-2"></i>Reindirizzamento...';
					
					setTimeout(() => { 
						window.location.href = data.redirect; 
					}, 1500);
				} else {
					alertBox.innerHTML = `
						<div class='alert alert-danger d-flex align-items-center'>
							<i class="bi bi-exclamation-triangle-fill me-2"></i>
							<div>
								<strong>Errore di accesso!</strong><br>
								<small>${data.message}</small>
							</div>
						</div>
					`;
					
					// Reset 
					submitBtn.disabled = false;
					submitBtn.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i>Accedi alla Dashboard';
					
					
					const container = document.querySelector('.login-container');
					container.style.animation = 'shake 0.5s ease-in-out';
					setTimeout(() => {
						container.style.animation = '';
					}, 500);
				}
			} catch (err) {
				alertBox.innerHTML = `
					<div class='alert alert-warning d-flex align-items-center'>
						<i class="bi bi-wifi-off me-2"></i>
						<div>
							<strong>Errore di connessione!</strong><br>
							<small>Verifica la connessione internet e riprova.</small>
						</div>
					</div>
				`;
				
				// Reset 
				submitBtn.disabled = false;
				submitBtn.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i>Accedi alla Dashboard';
			}
		});

		
		document.addEventListener('DOMContentLoaded', function() {
			const inputs = document.querySelectorAll('.form-control');
			
			inputs.forEach(input => {
				const formGroup = input.closest('.mb-4');
				
				input.addEventListener('focus', function() {
					if (formGroup) {
						formGroup.classList.add('focused');
					}
				});
				
				input.addEventListener('blur', function() {
					if (formGroup) {
						formGroup.classList.remove('focused');
					}
				});
			});
		});
	</script>
</body>
</html>
