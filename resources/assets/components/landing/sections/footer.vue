<template>
	<div class="footer-component">
		<div class="footer-component-links">
			<a href="/site/help">{{ $t('landing.footer.help') }}</a>
			<div class="spacer">·</div>
			<a href="/site/terms">{{ $t('landing.footer.terms') }}</a>
			<div class="spacer">·</div>
			<a href="/site/privacy">{{ $t('landing.footer.privacy') }}</a>
			<div class="spacer">·</div>
			<a href="https://pixelfed.org/mobile-apps" target="_blank">{{ $t('landing.footer.mobileApps') }}</a>
		</div>

		<div class="footer-component-attribution">
			<div><span>© {{ getYear() }} {{config.domain}}</span></div>
			<div class="spacer">·</div>
			<div><a href="https://pixelfed.org" class="text-bluegray-500 font-weight-bold">{{ $t('landing.footer.poweredByPixelfed') }}</a></div>
			<div class="spacer">·</div>
			<div><span>v{{config.version}}</span></div>
		</div>
	</div>
</template>

<script type="text/javascript">
	export default {
		data() {
			return {
				config: window.pfl
			}
		},

		methods: {
			getYear() {
				return (new Date().getFullYear());
			}
		}
	}
</script>
