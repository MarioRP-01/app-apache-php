# Frontend Configuration

## Overview

This project is a simple Apache and PHP application designed to test Apache's metrics export methods. However, I decided to explore possible frontend workflows that do not involve migrating to a javascript framework.

In this guide, we will see how to configure a bundler to compile and bundle our SASS and JS files so that we can use them in our project in our development and production environments.

## Steps

### 1: Create Dockerfile for Node

Inside the `frontend` directory, create a `Dockerfile` with the following content:

```dockerfile
ARG NODE_VERSION=20
ARG ALPINE_VERSION=3.17

FROM node:${NODE_VERSION}-alpine${ALPINE_VERSION}

ARG USER_ID
ARG GROUP_ID

WORKDIR /app

## Add user and group
RUN deluser --remove-home node
RUN addgroup -S user -g $GROUP_ID
RUN adduser -S -g user -u $USER_ID user

USER user

# Only during the setup process
CMD ["tail", "-f", "/dev/null"] 
```

As you can see, we are creating a custom user to replace the default `node` user. As the user id and group id may be different in each system. Remember to add the `USER_ID` and `GROUP_ID` variables to your `.env` file.

Since we haven't installed the dependencies yet, we will use the `CMD' command to keep the container running. So we can access it and install the dependencies through the container.

### 2. Add Node Service to Docker Compose

We will configure the Docker Compose so we can use a Node container to compile our SASS and JS files. This will allow us in the next steps to configure a process so that the files are compiled and bundled automatically when we make changes to them.

Add the following section to the `services` section of your `docker-compose.yml` file:

```yml
node:
    build:
    context: ./frontend
    args:
        USER_ID: ${UID}
        GROUP_ID: ${GID}
    container_name: node
    environment:
        - NODE_ENV=development
    volumes:
    - ./frontend:/app
    - ./app/public:/backend/public
```

A few things to note:

- We are passing the `USER_ID` and `GROUP_ID` variables to the container so that the user and group are created with the same ids as the specified in the `.env` file.
- We are mounting the `frontend` directory to the `/app` directory inside the container. This will allow us to access the files from the container and install the dependencies.
- We are mounting the `app/public` directory of our php application to the `/backend/public` directory of the node container. This will allow us compile the files and make them available to the php application.

### 3. Initialize Node With Webpack

Once the node environment is configured, we can initialize the node project. To do this, we will access the container and run the following command:

- Start docker-compose:

    ```shell
    docker compose up -d
    ```

- Access the container:

    ```shell
    docker exec -it node shell
    ```

- Initialize the project:

    ```shell
    npm init -y
    ```

- We will use ES6 modules, so we will need to add the following line to the `package.json` file:

    ```json
    {
        "type": "module"
    }
    ```

- install webpack dependencies:

    ```shell
    npm install webpack webpack-cli -D
    ```

- create webpack config file inside a `build` directory:

    In our case, we will create create three files inside the `build` directory, one common file and two environment specific files:

  - `paths.cjs`: This file will contain the paths to the source and distribution directories.

    ```js
    const path = require('path')

    module.exports = {
        SRC: path.resolve(__dirname, '..', 'src'),
        DIST: '/backend/public'
    }
    ```

    Our source files will be located in the `src` directory and the compiled files will be placed in the `public` directory of our php application.

    The extensions `.cjs` is used to indicate that we are using commonjs modules. This is necessary because we are using the `type: module` option in the `package.json` file.

  - `webpack.config.base.cjs`: This file will contain the common configuration for both environments.

    ```js
    const { SRC, DIST } = require('./paths.cjs')

    module.exports = {
        context: SRC,
        entry: {
            main: { import: './main.js' },
        },
        output: {
            path: DIST,
            filename: 'js/[name].js'
        }
    }
    ```

  - `webpack.config.dev.cjs`: This file will contain the configuration for the development environment.

    ```js
    const { merge } = require('webpack-merge')

    module.exports = merge(require('./webpack.config.base.cjs'), {
    mode: 'development',
    watch: true,
    devtool: 'source-map'
    })
    ```

    The `watch` option will allow webpack to monitor the files and recompile them when they change and the devtool option will allow us to `debug` the original files instead of the compiled files.

  - `webpack.config.prod.cjs`: This file will contain the configuration for the production environment.

    ```js
    const { merge } = require('webpack-merge')

    module.exports = merge(require('./webpack.config.base.cjs'), {
    mode: 'production',
    })
    ```

  - Add the following scripts to the `package.json` file:

    ```json
    {
        "scripts": {
            "dev": "webpack --config build/webpack.config.dev.cjs",
            "prod": "webpack --config build/webpack.config.prod.cjs"
        }
    }
    ```

- Once the dev script is complete, we can edit the `Dockerfile` to run the `dev` script when the container starts.

    ```dockerfile
    CMD ["npm", "run", "dev"]
    ```

### 3. Add browserlist configuration

This configuration will be used by swc and `postcss-preset-env` plugin to determine which polyfills to use. It allow us to **define the browsers we want to support**.

- Create a file called `.browserlistrc` with the configuration for the browsers we want to support:

    ```browserslist
    last 2 versions
    > 0.5%
    IE 10
    ```

    This configuration will allow us to support the last two versions of the most popular browsers, browsers with more than 0.5% of the market share and IE 10. This configuration is just an example, you can use the [browserslist generator](https://browserl.ist/) to generate your own configuration.

### 4. Add configuration for JavaScript

- Install swc dependencies

    ```shell
    npm install -D \
        swc-loader \
        @swc/core
    ```

    `swc-loader` is a Webpack loader for swc. `@swc/core` is a super-fast javascript / typescript compiler written in rust.

  - Add the following configuration to the `webpack.config.base.cjs` file:

    ```js
    const jsRules = {
        test: /\.m?js$/,
        exclude: /(node_modules)/,
        use: {
            loader: 'swc-loader'
        }
    }

    module.exports = {
        module: {
            rules: [
                jsRules
            ],
        },
        plugins
    }
    ```

- Add js linter:

    We will use `standard` to lint our javascript files.

    ```shell
    npm install standard -D
    ```

  - Add the following configuration to the `package.json` file:

    ```json
    "scripts": {
        "lint": "standard --fix"
    },
    ```

    Allow us to use the command `npm run lint` to lint our javascript files.

    ```json
    "eslintConfig": {
        "extends": "./node_modules/standard/eslintrc.json"
    },
    ```

    Allow us to extend the standard configuration to eslint.

    The `eslintConfig` option allows us to specify the configuration for eslint. In this case, we are extending the configuration provided by `standard`.

### 5. Add configuration for SASS

- Install the dependencies to compile CSS with WebPack  

    ```shell
    npm install -D \
        mini-css-extract-plugin \
        css-loader
    ```

    The `mini-css-extract-plugin` allows WebPack to extract the compiled css to another file. `style-loader` is an alternative that loads the css inline, which is not convenient for our use case.

  - Add the following configuration to the `webpack.config.base.cjs` file:

    ```js
    const { SRC, DIST } = require('./paths.cjs')
    const MiniCssExtractPlugin = require('mini-css-extract-plugin')

    const plugins = [
        new MiniCssExtractPlugin({
            filename: 'css/[name].css',
            chunkFilename: 'css/[id].css'
        }),
    ]

    const cssRules = {
        test: /\.css$/i,
        use: [MiniCssExtractPlugin.loader, 'css-loader'],
    }

    module.exports = {
        module: {
            rules: [
                jsRules,
                cssRules
            ],
        },
        plugins
    }
    ```

    This configuration will allow us to compile css files and extract them to the `public` directory of our php application.

- Install the dependencies to compile SASS with WebPack  

    ```shell
    npm install -D \
        sass \
        sass-loader \
        resolve-url-loader
    ```

    The `sass-loader` allows WebPack to compile SASS files to CSS. The `resolve-url-loader` allows WebPack to resolve relative paths in the SASS files.

  - Add the following configuration to the `webpack.config.base.cjs` file inside the `cssRules`:
  
    ```js
    const cssRules = {
        test: /\.(s[ac]|c)ss$/i,
        use: [
            MiniCssExtractPlugin.loader,
            'css-loader',
            'resolve-url-loader',
            'sass-loader'
        ]
    }
    ```

- Add PostCSS to the project

    PostCSS is a tool for transforming CSS with JavaScript. PostCSS itself does not do anything. It is a framework for creating plugins that transform CSS. PostCSS plugins can do anything from linting your CSS to optimizing it for the browser.

    ```shell
    npm install -D \
        postcss \
        postcss-preset-env \
        postcss-loader
    ```

  - Add the following configuration to the `webpack.config.base.cjs` file inside the `cssRules`:

    ```js
    const cssRules = {
        test: /\.(s[ac]|c)ss$/i,
        use: [
            MiniCssExtractPlugin.loader,
            'css-loader',
            'postcss-loader',
            'resolve-url-loader',
            'sass-loader'
        ]
    }
    ```

    `postcss-loader` must be placed before `css-loader` and `MiniCssExtractPlugin.loader` and after `sass-loader`. [See More](https://webpack.js.org/loaders/postcss-loader/#config-cascade).

  - Create a `postcss.config.js` file with the following content:

    ```js
    module.exports = {
        plugins: ['postcss-preset-env']
    }
    ```

    `postcss-preset-env` allow us to convert modern CSS into something that most browsers can understand, by specifying the polyfills we need based on our target browsers in the `.browserlistrc` file.

- Add scss linter

  - Install dependencies

    ```shell
    npm install stylelint stylelint-scss stylelint-config-standard-scss -D
    ```

  - Add the following configuration to the `package.json` file:

    ```json
    "scripts": {
        "lint": "standard --fix && stylelint 'src/**/*.{css,scss}' --fix"
    },
    ```

    Extend the `lint` script to check all css and scss files inside the src directory.

    ```json
    {
        "stylelint": {
            "extends": "stylelint-config-standard-scss"
        }
    }
    ```

### 6. Add configuration for svg

- Add configuration to the `webpack.config.base.cjs` file:

    ```js
    const iconsRules = {
        mimetype: 'image/svg+xml',
        scheme: 'data',
        type: 'asset/resource',
        generator: {
            filename: 'img/[hash].svg'
        }
    }

    module.exports = {
        module: {
            rules: [
                jsRules,
                cssRules,
                iconsRules
            ],
        },
        plugins
    }
    ```

### 7. Add Boostrap

- Install dependencies

    ```shell
    npm i --save bootstrap @popperjs/core
    ```

    ```shell
    npm i autoprefixer -D
    ```

    Autoprefixer is used to add vendor prefixes to CSS rules using values from Can I Use. It is recommended by Google and used in Twitter and Taobao.

  - Add the following configuration to the `webpack.config.base.cjs` file inside the `cssRules`:

    ```js
    use: [
        MiniCssExtractPlugin.loader,
        'css-loader',
        {
            loader: 'postcss-loader',
            options: {
                postcssOptions: {
                plugins: [
                    autoprefixer
                ]
                }
            }
        },
        'resolve-url-loader',
        'sass-loader'
    ]
    ```

    Now you can import boostrap using the followings line:

    ```css
    @import "bootstrap/scss/bootstrap";
    ```

    ```js
    import { Tooltip, Toast, Popover } from 'bootstrap'
    // or
    import * as bootstrap from 'bootstrap'
    ```

## References

- [Webpack with PHP](https://stackoverflow.com/questions/43436754/using-webpack-with-an-existing-php-and-js-project)
- [Set CSS entry](https://webpack.js.org/guides/entry-advanced/)
- [Webpack with Boostrap](https://getbootstrap.com/docs/5.3/getting-started/webpack/)
- [PostCss location](https://webpack.js.org/loaders/postcss-loader/#config-cascade)
- [PostCss Preset Env](https://github.com/csstools/postcss-preset-env)
