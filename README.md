# Moovi

A [simple site](https://moovi.robr.app) that I put together to practice using OpenAI's API and PHP SDK. 

Right now, there are two components:
- **frontend**
	- request and display random movie from pre-saved movies (see below)
	- using AlpineJS to render
- **backend**
	- get movie
		- URL: https://api.moovi.robr.app/get-movie
		- accepts two parameters:
			- limit (default: 1)
				- number of movies to fetch
				- can be between one and five
			- exclude (default: null)
				- IDs of movies to exclude 
				- helps with perceived randomness of movie results by preventing same set of movies from being returned in subsequent responses
	- create movie
		- URL: https://api.moovi.robr.app/create-movie
			- requires special authorization code (I ain't tellin')
		- app will:
			- create a random prompt from datasets of characters, plots, and genres
			- request title, tagline, and production shot of movie based on prompt from OpenAI
		- since OpenAI takes around 20-30 seconds to generate assets for a movie, I'm using a cronjob to create a new movie every three hours
			- I'll probably scale this back to one movie a day once I have a large enough dataset

May this app provide you some amusement and nightmare fuel!