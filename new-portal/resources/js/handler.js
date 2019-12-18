/**
 * This is a Singleton 'handler'
 * it's responsible by handle with validation inputs
 */
const handler =  (() => {
    return mObj = {
        regex: (() => {
            return {
                /**
                 * Regex value
                 *
                 * @var regex RegExp
                 */
                regex: / /m,

                /**
                 * Flush the regex
                 *
                 * @returns void
                 */
                flush: () => {
                    this.regex = / /m;
                },

                /**
                 * Defines a regex to the next validation
                 *
                 * @param regex String
                 */
                defineRegex: (regex) => {
                    this.regex = regex;
                },
            }
        }),

        /**
         * Validates a value with the specific rules
         *
         * @param value any?
         */
        validate: (value) => {
            // verifies if the regex is filled
            if(handler().regex.regex != / /m) {
                // regex is filled

                // verifies if the value match with the last regex
                if(! handler().regex().regex.test(value)) {
                    handler().regex().flush();
                    return false;
                }
            }


        }
    }
})

handler().regex().defineRegex(/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}$/m);
handler().validate('123');
