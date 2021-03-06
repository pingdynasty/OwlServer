'use strict';

const patchFieldValidators = require('./patch-field-validators');

/**
 * Patch validation error.
 */
class PatchValidationError extends Error {

  /**
   * Class constructor.
   *
   * @param {string} message
   */
  constructor(field, message = 'Illegal patch.') {
    super(message);
    this.field = field;
    this.type = 'field_required';
    this.public = true;
  }
}

/**
 * Patch model.
 */
class Patch {

  /**
   * Patch validator.
   *
   * @throws {PatchValidationError}
   */
  validate() {
    for (let field in patchFieldValidators) {

      if (typeof this[field] === 'undefined') {

        // Check for required fields
        if (patchFieldValidators[field].required === true) {
          throw new PatchValidationError(field, `Field '${field}' is required.`);
        }

        // instructions and description are required if patch is published
        if (this.published && (field === 'instructions' || field === 'description')) {
          throw new PatchValidationError(field, `Field '${field}' is required.`);
        }
      } else {

        // Validate single fields
        patchFieldValidators[field].validate(this[field]);
        if (patchFieldValidators[field].sanitize) {
          // if a sanitization function exist, call it and then revalidate
          // just in case...
          this[field] = patchFieldValidators[field].sanitize(this[field]);
          patchFieldValidators[field].validate(this[field]);
        }
      }
    }
  }

  /**
   * Patch sanitizer.
   */
  sanitize() {

    // Default values:
    if (!this.inputs) {
      this.inputs = 0;
    }
    if (!this.outputs) {
      this.outputs = 0;
    }
    if (!this.parameters) {
      // set default parameters
      this.parameters = [{
        id: 0,
        name: "A",
        io: "input",
        type: "float"
      },
      {
        id: 1,
        name: "B",
        io: "input",
        type: "float"
      },
      {
        id: 2,
        name: "C",
        io: "input",
        type: "float"
      },
      {
        id: 3,
        name: "D",
        io: "input",
        type: "float"
      },
      {
        id: 4,
        name: "Exp",
        io: "input",
        type: "float"
      },
      {
        id: 80,
        name: "Pushbutton",
        io: "input",
        type: "bool"
      }];

    }
    if (!this.compilationType) {
      this.compilationType = 'cpp';
    }
    this.published = this.published ? true : false;
    if (!this.github) {
      this.github = [];
    }
    if (!this.downloadCount) {
      this.downloadCount = 0;
    }
    if (!this.starList) {
      this.starList = [];
    }
    // The below fields should all be generated somewhere else:
    // - seoName
    // - creationTimeUtc

    if (this.author && this.author.wordpressId && this.author.name) {
      delete this.author.name;
    }

    // Delete unrecognized fields
    var keys = Object.keys(patchFieldValidators);
    for (let key in this) {
      if (keys.indexOf(key) === -1) {
        delete this[key];
      }
    }
  }

  /**
   * Random patch name generator.
   */
  generateRandomName() {
    if (!this.name) {
      const randomId = () => (Math.random() * 0xFFFF<<0).toString(16);
      this.name = 'untitled-' + randomId() + randomId() + randomId();
    }
  }

  /**
   * SEO name generator.
   */
  generateSeoName() {
    this.seoName = this.name.replace(/[^a-z0-9]+/gi, '_');
  }
}

module.exports = Patch;
