import Alpine from 'alpinejs'
import axios from 'axios';



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
			axios
				.get( 'https://moovi.robr.app/endpoint/get-movie' )
				.then( res => {
					const { data: { movies } } = res

					if( !Array.isArray( movies ) || !movies.length ) {
						this.showError( 'Oops! Couldn\'t load a movie!' )
					} else {
						this.displayMovie( movies[ Math.floor( Math.random() * movies.length ) ] )
					}
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


}) ()