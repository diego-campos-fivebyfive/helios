<template lang="pug">
	.frameview
		iframe.frameview-frame(:src='getRoute')
</template>

<script>
	export default {
		computed: {
			getRoutePath() {
				const currentPath = this.$route.fullPath
				const homePath = '/dashboard'

				return (currentPath === '/')
					? homePath
					: currentPath
			},
			getRoute() {
				const { absolutePath } = this.$route.meta

				if (!absolutePath) {
					const twigBaseUri = `${process.env.API_URL}/twig`
					const routePath = this.getRoutePath
					return `${twigBaseUri}${routePath}`
				}

				const viewsEntryPoint = 'twig'
				const viewsEntryPointIndex = absolutePath
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
		}
	}
</script>

<style lang="scss" scoped>
	.frameview {
		height: 100%;
		overflow: hidden;
	}

	.frameview-frame {
		border: 0;
		height: 100%;
		width: 100%;
	}
</style>
