# Giving Impact API 2.1 Data Spec

**Version 3**

## Terms and Notes

  * SIGNED INT is defined as a non-negative integer
  * FLOAT is defined as floating point containing EXACTLY two decimal places
  * ENUM is defined as a restricted list
  * OBJECT is defined as JSON encoded string object
  * BOOLEAN values will be returned as either `true` or `false`
  * ellipses (...) indicates definition repeats
  * Vertical pipe (|) indicates multiple type options

Notes:

  * All data is returned as JSON encoded strings, by default.
  * BOOLEAN values will **not** be returned as strings, for example
    * Correct: `"error": false`
    * Incorrect: `"error": "false"`
  * Objects will never be nested more than two levels deep, to avoid recursion (i.e. no Donation -> Campaign -> Donations)
  * All STRINGS are URL encoded
  * All method will return a list, even if method only returns a single item (i.e. a single campaign will return an array with a single element)

## Status

  * HTTP/1.1 200 Found<br />
    Request was valid
  * HTTP/1.1 3XX Moved<br />
    Request URI has moved to a different location
  * HTTP/1.1 400 Bad Request<br />
    Request was not properly formed or contained an invalid property.
  * HTTP/1.1 401 Unauthorized<br />
    Request did not contain a valid API key
  * HTTP/1.1 402 Request Failed<br />
    Request was valid and authorized but failed for another reason which will be specified in the response
  * HTTP/1.1 404 Not Found<br />
    Requested URL was not routable
  * HTTP/1.1 5XX Server Error<br />
    Server was not able to respond to the request due to configuration or software error


## Errors

Errors will always be in the format of:

	{
		"error": TRUE,
		"message": STRING
	}

All successful API transactions will carry an `"error": FALSE` flag as the first element.

### Examples

  * 400 Bad Request
    * Request missing required properties
  * 401 Unauthorized
    * Request didn't contain valid API key
    * Request didn't contain User-Agent header
    * Request attempting to access resources outside of account
  * 402 Request Failed
    * POSTed data was truncated
    * Content-Length missmatch
    * POSTed data contains unprocessable strings
  * 404 Not Found
    * Accessing a non-routable URL scheme

## Campaigns

### Input

	{
		"title"						: STRING,
		"description"				: STRING,
		"youtube_id"				: STRING,
		"youtube_url"				: STRING,
		"donation_target"			: FLOAT,
		"hash_tag"					: STRING,
		"status"					: BOOLEAN,

		"donation_levels"			: [
			{
				"amount"	: FLOAT,
				"label"		: STRING,
			},
			...
		]
									OR
									: FALSE,

		"has_giving_opportunities"	: BOOLEAN,
		"display_total"				: BOOLEAN,
		"display_current"			: BOOLEAN,
		"image_file"				: BASE64 STRING,
		"image_type"				: ENUM (jpg OR gif OR png)
		"custom_fields"		: [
			{
				"field_id"		: SIGNED INT,
				"field_type"	: ENUM (text, select),
				"field_label"	: STRING,
				"options"		: [
					STRING,
					...
				],
				"position"		: SIGNED INT,
				"status"		: BOOLEAN,
				"required"		: BOOLEAN
			},
			...
		]
	}

When updating a campaign a valid campaign token is **required**. Failure to provide one will result in error `400`.

`image_file` will be provided as a BASE64 encoded string and must be accompanied by `image_type` ENUM. Providing one without the other will result in error `400`.

`youtube_id` and `youtube_url` result in the same data property. `youtube_id` contains only the resource ID. `youtube_url` contains a full YouTube URL from which the ID can be derived. API will prefer `youtube_id` if both values are given.

If a campaign is based upon donation levels, they must be provided as an array of donation level objects with the keys `amount`, a FLOAT for the donation amount, and `label` a text label for the donation amount. Otherwise, the value of `donation_levels` will be assumed to be `FALSE` and the campaign will have an open donation input.

The `custom_fields` parameter accepts an array of custom field objects. Any object with a `field_id` key is assumed to be updating an existing field, while an object without is assumed to be create a new field. The `options` key accepts an array of values for the "select" `field_type`. It is ignored if the `field_type` is set to "text".

### Return

Campaign methods will return data in the format of:

	{
  		"error": FALSE,
  		"campaigns": [
  			{
				"id_token"					: STRING,
				"title"						: STRING,
				"description"				: STRING,
				"youtube_id"				: STRING,
				"donation_total"			: FLOAT,
				"donation_target"			: FLOAT,
				"givlink"					: STRING,
				"donation_url"				: STRING,
				"share_url"					: STRING,
				"shares_fb"					: SIGNED INT,
				"shares_twitter"			: SIGNED INT,
				"hash_tag"					: STRING,
				"status"					: BOOLEAN,
				"has_donation_levels"		: BOOLEAN,
				"donation_levels"			: [
					{
						"amount"	: FLOAT,
						"label"		: STRING,
					},
					...
				],
											OR
											: FALSE,
				"has_giving_opportunities"	: BOOLEAN,
				"display_total"				: BOOLEAN,
				"display_current"			: BOOLEAN,
				"image_url"					: STRING
				"custom_fields"		: [
					{
						"field_id"		: SIGNED INT,
						"field_type"	: STRING,
						"field_label"	: STRING,
						"options"		: [
							STRING,
							...
						],
						"position"		: SIGNED INT,
						"status"		: BOOLEAN,
						"required"		: BOOLEAN
					},
					...
				]
			},
			...
		]
	}


## Opportunities

### Input

Opportunity methods accept POSTed data in the format of:

	{
		"campaign_token"			: STRING,
		"title"						: STRING,
		"description"				: STRING,
		"youtube_id"				: STRING,
		"youtube_url"				: STRING,
		"donation_target"			: FLOAT,
		"hash_tag"					: STRING,
		"status"					: BOOLEAN,
		"image_file"				: BASE64 STRING,
		"image_type"				: ENUM (jpg OR gif OR png)
	}

When creating a new opportunity, the `campaign_token` for the parent campaign is **required**. Changing the `campaign_token` after a campaign has been created will result in an error `400`.

When editing an existing opportunity, the opportunity's token will be given in the request's URL. Failing to provide either a valid opportunity token in the URL OR a valid `campaign_token` will result in an error `400`.

`image_file` will be provided as a BASE64 encoded string and must be accompanied by `image_type` ENUM. Providing one without the other will result in error `400`.

`youtube_id` and `youtube_url` result in the same data property. `youtube_id` contains only the resource ID. `youtube_url` contains a full YouTube URL from which the ID can be derived. API will prefer `youtube_id` if both values are given.

### Return

Opportunity methods will return data in the format of:

	{
  		"error": FALSE,
  		"opportunities": [
  			{
				"id_token"					: STRING,
				"campaign"					: OBJECT | STRING,
				"title"						: STRING,
				"description"				: STRING,
				"youtube_id"				: STRING,
				"donation_total"			: FLOAT,
				"donation_target"			: FLOAT,
				"givlink"					: STRING,
				"donation_url"				: STRING,
				"share_url"					: STRING,
				"shares_fb"					: SIGNED INT,
				"shares_twitter"			: SIGNED INT,
				"hash_tag"					: STRING,
				"status"					: BOOLEAN,
				"image_url"					: STRING
			},
			...
		]
	}

## Donations

### Input

Note, at the moment, the only acceptable donation input is an "offline" donation.

	{
		"campaign"				: STRING | BOOLEAN,
		"opportunity"			: STRING | BOOLEAN,
		"first_name" 			: STRING,
		"last_name"				: STRING,
		"billing_address1"		: STRING,
		"billing_city"			: STRING,
		"billing_state"			: STRING,
		"billing_postal_code"	: STRING,
		"billing_country"		: STRING,
		"individual_total"		: FLOAT,
		"donation_level"		: STRING,
		"email_address"			: STRING | BOOLEAN,
		"contact"				: BOOLEAN,
		"offline"				: TRUE,
		"custom_responses"		: [
			{
				"field_id"		: STRING,
				"field_type"	: STRING,
				"field_label"	: STRING,
				"response"		: STRING,
				"status"		: BOOLEAN
			},
			...
		]
	}

Either `campaign` or `opportunity` is required. If both are supplied, `opportunity` will be preferred. If neither is provided, error `400` will be returned.

As "offline" donations are the only acceptable input at this time, `offline` must be `true`. Any other value will result in error `400`.

`custom_responses` accepts an array of custom response objects. Each object must contain a valid `field_id`. Failure to provide valid `field_id` will result in error `400`. A **required** custom response that is not provided by the request will result in error `400`.

### Return

Donation methods will return data in the format of:

	{
		"error": FALSE,
		"donations": [
			{
				"campaign"				: OBJECT | STRING | BOOLEAN,
				"opportunity"			: OBJECT | STRING | BOOLEAN,
				"first_name" 			: STRING,
				"last_name"				: STRING,
				"billing_address1"		: STRING,
				"billing_city"			: STRING,
				"billing_state"			: STRING,
				"billing_postal_code"	: STRING,
				"billing_country"		: STRING,
				"individual_total"		: FLOAT,
				"donation_level"		: STRING,
				"email_address"			: STRING | BOOLEAN,
				"contact"				: BOOLEAN,
				"referrer"				: STRING,
				"offline"				: BOOLEAN,
				"created_at"			: TIMESTAMP,
				"twitter_share"			: BOOLEAN,
				"fb_share"				: BOOLEAN,
				"offline_donation"		: BOOLEAN,
				"custom_responses"		: [
					{
						"field_type"	: STRING,
						"field_label"	: STRING,
						"response"		: STRING,
						"status"		: BOOLEAN
					},
					...
				]
			},
			...
		]
	}

If `related=campaign` or `related=opportunity` is passed to API method, the `campaign` or `opportunity` field will be populated by associated donation's campaign or opportunity object (as described in those sections). Otherwise, field will contain campaign or opportunity token.

#### Example

	{
		"error": FALSE,
		"donations": [
			{
				"campaign"				: {
						"id_token"			: STRING,
						"title"				: STRING,
						"description"		: STRING,
						"youtube_id"		: STRING,
						"donation_total"	: FLOAT,
						"donation_target"	: FLOAT,
						"givlink"			: STRING,
						"donation_url"		: STRING,
						"share_url"			: STRING,
						"shares_fb"			: SIGNED INT,
						"shares_twitter"	: SIGNED INT,
						"hash_tag"			: STRING,
						"status"			: BOOLEAN,
						"image_url"			: STRING
				},
				"opportunity"			: BOOLEAN,
				"first_name" 			: STRING,
				"last_name"				: STRING,
				"billing_address1"		: STRING,
				"billing_city"			: STRING,
				"billing_state"			: STRING,
				"billing_postal_code"	: STRING,
				"billing_country"		: STRING,
				"individual_total"		: FLOAT,
				"donation_level"		: STRING,
				"email_address"			: STRING | BOOLEAN,
				"contact"				: BOOLEAN,
				"referrer"				: STRING,
				"offline"				: BOOLEAN,
				"created_at"			: TIMESTAMP,
				"twitter_share"			: BOOLEAN,
				"fb_share"				: BOOLEAN,
				"offline_donation"		: BOOLEAN,
				"custom_responses"		: [
					{
						"field_type"	: STRING,
						"field_label"	: STRING,
						"response"		: STRING,
						"status"		: BOOLEAN
					},
					...
				]
			},
			...
		]
	}

