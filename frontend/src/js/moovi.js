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
		this.showErrorMsg()
		this.states.isLoading = true

		this.fetchMovie().then( movie => {
			this.data.movie = movie

		}).catch( err => {
			this.showErrorMsg( err.message )

		}).finally( () => {
			this.states.isLoading = false
		})
	},


	/**
	 * Fetch movies from backend
	 *
	 * @return {Promise}
	 */
	fetchMovie() {
		const endpointUrl = new URL( this.data.endpointUrl )

		// avoid showing the same movie twice
		if ( this.data.movie.id && !Number.isNaN( this.data.movie.id ) ) {
			endpointUrl.searchParams.set( 'exclude', this.data.movie.id )
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
	 * Show error message
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
