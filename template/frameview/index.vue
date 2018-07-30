<template lang="pug">
	.frameview
		iframe.frameview-frame(:src='getRoute')
</template>

<script>
	export default {
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
</style>
