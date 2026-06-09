/**
 * Normalises any string format into an array of lowercase words.
 * Handles: snake_case, kebab-case, camelCase, PascalCase, plain text, SCREAMING_SNAKE_CASE,
 * and strings with punctuation (slashes, dots, etc.).
 */
const normaliseToWords = (text) => {
  if (!text) return []

  return text
    // Handle consecutive uppercase (acronyms) followed by a capitalised word
    .replace(/([A-Z]+)([A-Z][a-z])/g, '$1 $2')
    // Insert space before uppercase letters (for camelCase/PascalCase)
    .replace(/([a-z\d])([A-Z])/g, '$1 $2')
    // Replace common delimiters and punctuation with spaces
    .replace(/[_\-./\\:,;|]+/g, ' ')
    // Collapse multiple spaces
    .replace(/\s+/g, ' ')
    .trim()
    .toLowerCase()
    .split(' ')
    .filter((word) => word.length > 0)
}

/**
 * Capitalises the first letter of a word.
 */
const capitaliseWord = (word) => {
  if (!word) return ''
  return word.charAt(0).toUpperCase() + word.slice(1)
}

/**
 * Converts any string format to snake_case.
 * @example textToSnakeCase('helloWorld') // 'hello_world'
 * @example textToSnakeCase('Hello World') // 'hello_world'
 * @example textToSnakeCase('hello-world') // 'hello_world'
 */
const textToSnakeCase = (text) => {
  return normaliseToWords(text).join('_')
}

/**
 * Converts any string format to kebab-case.
 * @example textToKebabCase('helloWorld') // 'hello-world'
 * @example textToKebabCase('Hello World') // 'hello-world'
 * @example textToKebabCase('hello_world') // 'hello-world'
 */
const textToKebabCase = (text) => {
  return normaliseToWords(text).join('-')
}

/**
 * Converts any string format to PascalCase.
 * @example textToPascalCase('hello_world') // 'HelloWorld'
 * @example textToPascalCase('hello world') // 'HelloWorld'
 * @example textToPascalCase('hello-world') // 'HelloWorld'
 */
const textToPascalCase = (text) => {
  return normaliseToWords(text).map(capitaliseWord).join('')
}

/**
 * Converts any string format to camelCase.
 * @example textToCamelCase('hello_world') // 'helloWorld'
 * @example textToCamelCase('Hello World') // 'helloWorld'
 * @example textToCamelCase('hello-world') // 'helloWorld'
 */
const textToCamelCase = (text) => {
  const words = normaliseToWords(text)
  return words
    .map((word, index) => (index === 0 ? word : capitaliseWord(word)))
    .join('')
}

/**
 * Converts any string format to Title Case (space-separated capitalised words).
 * @example textToTitleCase('hello_world') // 'Hello World'
 * @example textToTitleCase('helloWorld') // 'Hello World'
 */
const textToTitleCase = (text) => {
  return normaliseToWords(text).map(capitaliseWord).join(' ')
}

/**
 * Converts any string format to plain lowercase text (space-separated).
 * @example textToLowerCase('HelloWorld') // 'hello world'
 * @example textToLowerCase('hello_world') // 'hello world'
 */
const textToLowerCase = (text) => {
  return normaliseToWords(text).join(' ')
}

/**
 * Converts any string format to SCREAMING_SNAKE_CASE.
 * @example textToScreamingSnakeCase('helloWorld') // 'HELLO_WORLD'
 */
const textToScreamingSnakeCase = (text) => {
  return normaliseToWords(text).join('_').toUpperCase()
}

export {
  normaliseToWords,
  capitaliseWord,
  textToSnakeCase,
  textToKebabCase,
  textToPascalCase,
  textToCamelCase,
  textToTitleCase,
  textToLowerCase,
  textToScreamingSnakeCase,
}
