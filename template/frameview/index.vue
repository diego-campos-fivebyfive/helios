<template lang="pug">
	.frameview
		.frameview-loading(v-if='loadingTwig')
			img.frameview-loading-img(
				src='~theme/assets/media/loading.gif')
		iframe.frameview-frame(:src='getRoute', ref='twig')
</template>

<script>
	export default {
		data:() => ({
			loadingTwig: true
		}),
		mounted() {
			this.routeTwigLoaded()
		},
		computed: {
			getRoute() {
				const currentPath = this.$route.fullPath

				if (currentPath === '/') {
					const homePath = 'twig/dashboard'
					return `${process.env.API_URL}/${homePath}`
				}

				const viewsEntryPoint = 'twig'
				const viewsEntryPointIndex = this.$route.meta.absolutePath
					.split('/')
					.filter(segment => segment)
					.findIndex(segment => segment === viewsEntryPoint)

				const currentPathSegments = this.$route.fullPath
					.split('/')
					.filter(segment => segment)

				return currentPathSegments.reduce((acc, segment, segmentIndex) => (
					(segmentIndex === viewsEntryPointIndex)
						? `${acc}/${viewsEntryPoint}/${segment}`
						: `${acc}/${segment}`
				), process.env.API_URL)
			}
		},
		methods: {
			routeTwigLoaded() {
				this.$refs.twig.onload = () => {
					this.loadingTwig = true
				}
			}
		}
	}
</script>

<style lang="scss" scoped>
  .frameview {
    height: 100%;
    overflow: hidden;
    width: 100%;
  }

  .frameview-frame {
    border: 0;
    height: 100%;
    width: 100%;
	}

	.frameview-loading {
		position: fixed;
		width: 100%;
		z-index: -1;
    margin-top: 70vw;
    text-align: center;
	}

	.frameview-loading-img {
		opacity: 0.5;
		width: 40px;
	}
</style>
