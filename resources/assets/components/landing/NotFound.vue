<template>
	<div class="landing-index-component h-100">
		<section class="page-wrapper h-100 d-flex flex-grow-1 justify-content-center align-items-center">
			<div class="d-flex flex-column align-items-center gap-3">
				<i class="fal fa-exclamation-triangle fa-5x text-bluegray-500"></i>
				<div class="text-center">
					<h2>{{ $t('landing.notfound.header') }}</h2>
					<p class="lead">{{ $t('landing.notfound.description') }}</p>
				</div>
				<a class="btn btn-outline-light btn-lg rounded-pill px-4" href="/">{{ $t('landing.notfound.goback') }}</a>
			</div>
		</section>
	</div>
</template>
