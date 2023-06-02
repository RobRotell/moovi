// import axios, { isCancel, AxiosError } from 'axios'


export default () => ({


	states: {
		isLoading: false,
		isError: false,
	},


	data: {
		endpointUrl: 'https://moovi.robr.app/endpoints/get-movie',
		movie: {
			id: null,
			title: '',
			tagline: '',
			genre: '',
			director: '',
			year: '',
			image: {},
		},
		error: '',
		IdsToExclude: [],
	},


	/**
	 * Initiate the good stuff
	 *
	 * @return {void}
	 */
	init() {
		this.refreshMovie()
	},


	/**
	 * Show (or refresh if already showing) movie
	 *
	 * @return {self}
	 */
	refreshMovie() {
		if ( this.states.isLoading ) {
			return
		}

		this.showErrorMsg()
		this.states.isLoading = true

		// API is really darn fast; let's give a small delay to avoid jumping content
		setTimeout( () => {
			this.fetchMovie().then( movie => {

				// clear out movie so it doesn't "shimmer"
				this.data.movie.image = {}

				this.data.movie = movie
				this.queueIdToExclude( movie.id )

			}).catch( err => {
				this.showErrorMsg( err.message )

			}).finally( () => {
				this.$nextTick().then( () => {
					this.states.isLoading = false
				})
			})
		}, 250 )
	},


	/**
	 * Fetch movies from backend
	 *
	 * @return {Promise}
	 */
	fetchMovie() {
		const endpointUrl = new URL( this.data.endpointUrl )

		// avoid showing the same movie twice
		if ( this.data.IdsToExclude.length ) {
			endpointUrl.searchParams.set( 'exclude', this.data.IdsToExclude )
		}

		return new Promise( ( resolve, reject ) => {
			fetch( endpointUrl.toString() )
				.then( res => {
					if ( !res.ok && 200 !== res.status ) {
						throw new Error( 'Invalid response from server.' )
					}

					return res.json()

				}).then( res => {
					if ( 'object' !== typeof res && !Array.isArray( res.movies ) ) {
						throw new Error( 'Unexpected response from server.' )
					}

					resolve( res.movies[0] )

				}).catch( err => {
					this.logErrMessage( err )
					reject( new Error( 'Ran into an error. Please refresh the page!' ) )
				})
		})
	},


	/**
	 * Improve movie randomness by excluding past 10 movies through endpoint
	 *
	 * @param {int} id Movie ID
	 * @return {self}
	 */
	queueIdToExclude( id ) {
		if ( 10 <= this.data.IdsToExclude.length ) {
			this.data.IdsToExclude.shift()
		}

		this.data.IdsToExclude.push( id )
	},


	/**
	 * Show error message
	 * 
	 * @todo Add error HTML
	 *
	 * @param {string} msg Error message
	 * @return {self}
	 */
	showErrorMsg( msg = '' ) {
		this.data.error = msg
		this.states.isError = !!msg

		return this
	},


	/**
	 * Log error to console
	 *
	 * @param {Error} err Error to log
	 * @return {void}
	 */
	logErrMessage( err ) {
		window.console.warn( `Moovi :: Error: "${err.message}"` )
	},

})
