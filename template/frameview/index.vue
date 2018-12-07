<template lang="pug">
  transition(name='fade')
    .frameview
      .frameview-loading(v-show='frameLoading', :class='platform')
        img.frameview-loading-img(
          src='~theme/assets/media/loading.gif')
      iframe.frameview-frame(:src='getRoute', ref='frame')
</template>

<script>
	export default {
		data:() => ({
			frameLoading: true,
			platform: process.env.PLATFORM !== 'web' ? 'mobile' : 'web'
		}),
		mounted() {
			this.frameLoad()
		},
		computed: {
			getRoute() {
				const currentPath = this.$route.fullPath
        const viewsEntryPoint = 'twig'

				if (currentPath === '/') {
          const homePath = `${viewsEntryPoint}/dashboard`
					return `${process.env.API_URL}/${homePath}`
        }

        if (currentPath.includes('/#/')) {
          const path = currentPath.replace('/#/', '')
					return `${process.env.API_URL}/${viewsEntryPoint}/${path}`
        }

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
			frameLoad() {
				this.$refs.frame.onload = () => {
					this.frameLoading = false
				}
			}
		}
	}
</script>

<style lang="scss" scoped>
	$loading-img: 40px;

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
		width: $loading-img;
	}

  .web {
    display: none;
  }

	@media screen and (min-width: $ui-size-md) {
		.mobile {
			display: none;
		}
  }

  .fade-enter-active {
    transition: opacity 1s ease-in-out;
  }

  .fade-enter-to {
    opacity: 1;
  }

  .fade-enter {
    opacity: 0;
  }
</style>
