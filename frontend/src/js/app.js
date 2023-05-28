import Alpine from 'alpinejs'


( () => {


	const moovi = {

		states: {
			isLoading: false,
			isError: false,
		},


		data: {
			movie: {
				title: '',
				tagline: '',
				director: '',
				year: '',
				image: '',
			},
			error: '',
		},


		/**
		 * Initiate the good stuff
		 *
		 * @return {void}
		 */
		init() {
			const movie = this.fetchMovie()
		},


		/**
		 * Fetch movies from backend
		 *
		 * @return {Promise}
		 */
		fetchMovie() {
			return new Promise( ( resolve, reject ) => {
				fetch( 'https://moovi.robr.app/endpoint/get-movie' )
					.then( res => {
						console.log( res )
						return res.json()
					})
					.then( res => {
						console.log( res )
					})
			})
		},


		/**
		 * Show error message
		 *
		 * @param {string} msg Error message
		 * @return {self}
		 */
		showError( msg = '' ) {
			return this
		},


		/**
		 * Display movie on frontend
		 *
		 * @param {obj} movie
		 * @return {self}
		 */
		displayMovie( movie ) {
			this.data.movie = movie

			return this
		},

	}



	document.addEventListener( 'alpine:init', () => {
		Alpine.store( 'moovi', moovi )
	})

	Alpine.start()


})()
