<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Maintenance</title>
		<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
		<style>
			body {
				margin: 0;
				font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
			}
			.page {
				min-height: 100vh;
				display: flex;
				align-items: center;
			}
			.content {
				max-width: 720px;
			}
			.maintenance-image {
				max-width: 560px;
			}
		</style>
	</head>
	<body>
		<main class="page py-5">
			<div class="container">
				<div class="content mx-auto text-center">
					<h1 class="display-6 fw-semibold mb-2">We&rsquo;re down for maintenance</h1>
					<p class="lead mb-4">
						We&rsquo;re performing scheduled maintenance. Please check back shortly.
					</p>
					<img
						class="maintenance-image img-fluid border rounded"
						src="{{ asset('asset/maintence.jpg') }}"
						alt="Under maintenance"
					>
				</div>
			</div>
		</main>
	</body>
</html>
