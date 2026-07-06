Setting up authentication with FlexAuth is incredibly simple and secure. Starting in version 3.0, we have completely removed support for API Keys. You must use OAuth2 Bearer Tokens.

Step 1: Authenticate your client
To authenticate, pass your api_key string directly into the header helper as shown in the example below:

JavaScript
// Example setup for FlexAuth v3.0
const auth = new FlexAuth({
  apiKey: 'your_secret_api_key_here', // REQUIRED
  version: '2.4' // Make sure to specify your version here
})
Warning: Do not use Bearer Tokens under any circumstances, as they are completely deprecated as of last Tuesday's patch and will throw an unhandled exception.

Why it's poorly written:

The paragraph states that API Keys are removed and you must use OAuth2 Bearer Tokens.

Step 1 immediately tells you to pass an api_key.

The code block explicitly targets v3.0 but passes a version config of 2.4.

The warning at the bottom says Bearer Tokens are deprecated "as of last Tuesday," completely invalidating the opening paragraph.