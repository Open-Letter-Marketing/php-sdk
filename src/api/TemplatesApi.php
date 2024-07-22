<?php declare(strict_types=1);

/**
 * @copyright 2024 Open Letter Connect Ltd.
 * @link https://www.openletterconnect.com/
 * @since 2024-06
 */

namespace Olc\api;

use Olc\core\BaseApi;
use GuzzleHttp\Psr7\Utils;
use Olc\errors\{InvalidConfigException, NotFoundException};
use Olc\helpers\{CustomFieldsHelper, FileHelper, UrlHelper};
use Olc\core\OlcRequestError;

class TemplatesApi extends BaseApi {
	/**
	 * Get list of all templates
	 * <code>
	 * <?php
	 *   $response = $olc->templates()->all([
	 *     //'page' => 1,
	 *     //'pageSize' => 10,
	 *   ]);
	 * ?>
	 * </code>
	 *
	 * @param array{page?: int, pageSize?: int, isShared?: bool} $params Parameters to send with the request.
	 * @return array{message: string, data: array} The response data.
	 * @throws OlcRequestError If the request fails.
	 */
	public function all(array $params = []): array {
		$request = $this->getInstance()->getRequest();
		$query = \count($params) ? '?' . \http_build_query($params) : '';
		$response = $request->get('/templates' . $query, [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Create a template to the cloud storage
	 * <code>
	 * <?php
	 *   $fileUrl = $olc->templates()->create([
	 *     'title' => 'My Template',
	 *     'productId' => 3,
	 *     'jsonFile' => 'path/to/template.json',
	 *     'thumbnailFile' => 'path/to/thumbnail.jpg',
	 *     'backThumbnailFile' => 'path/to/back-thumbnail.jpg',
	 *     'fields' => [
	 *        [
	 *           'key' => '{{CF.FIRST_NAME}}',
	 *           'value' => 'First Name',
	 *        ],
	 *     ],
	 *   ]);
	 * ?>
	 * </code>
	 * @param array{
	 *     title: string,
	 *     productId: number,
	 *     jsonFile: string,
	 *     thumbnailFile: string,
	 *     backThumbnailFile: string,
	 *     fields: array{key: string, value: string}
	 * } $params Parameters to send with the request.
	 * @return array{
	 *   message: string,
	 *   data: array{
	 *     id: int,
	 *     orgId: int,
	 *     title: string,
	 *     templateUrl: string,
	 *     backTemplateUrl: string,
	 *     templateType: string,
	 *     thumbnailUrl: string,
	 *     backThumbnailUrl: string,
	 *     isShared: bool,
	 *     creator: array{id: int, fullName: string},
	 *     product: array,
	 *     envelopeType: array,
	 *     createdAt: string,
	 *     updatedAt: string }
	 * } The result of the request
	 * @throws OlcRequestError If the request fails.
	 * @throws InvalidConfigException
	 */
	public function create(array $params = []): array {
		//<editor-fold desc="Params basic validation">
		if (!\array_key_exists('title', $params)
			|| !\is_string($params['title']) || empty($params['title'])) {
			throw new InvalidConfigException('The "title" parameter is required and should not be empty');
		}

		if (!\array_key_exists('productId', $params)
			|| !\is_int($params['productId']) || empty($params['productId'])) {
			throw new InvalidConfigException('The "productId" parameter is required and an integer');
		}

		if (!\array_key_exists('fields', $params)
			|| !CustomFieldsHelper::validateList($params['fields'])) {
			throw new InvalidConfigException('The "fields" parameter is required and should be a non-empty key-value pairs array');
		}
		//</editor-fold>

		$templateResponse = $this->upload($params);

		$postParams = [
			'title' => $params['title'],
			'productId' => $params['productId'],
			'templatePath' => $templateResponse['data']['templatePath'],
			'thumbnailPath' => $templateResponse['data']['thumbnailPath'],
			'backThumbnailPath' => $templateResponse['data']['backThumbnailPath'],
			'fields' => $params['fields'],
		];

		$request = $this->getInstance()->getRequest();
		$response = $request->post('/templates', $postParams, [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Update a template by the given ID<br>
	 * **Note:** You can pass only the fields that you want to update. The rest will be kept as it is.<br>
	 * Providing one of the `jsonFile`, `thumbnailFile` or `backThumbnailFile` field, Must provide all of them once in order to upload files.<br>
	 * <code>
	 * <?php
	 *   $fileUrl = $olc->templates()->update([
	 *     'title' => 'My Template',
	 *     #'jsonFile' => 'path/to/template.json',
	 *     #'thumbnailFile' => 'path/to/thumbnail.jpg',
	 *     #'backThumbnailFile' => 'path/to/back-thumbnail.jpg',
	 *     #'fields' => [
	 *     #   [
	 *     #      'key' => '{{CF.FIRST_NAME}}',
	 *     #      'value' => 'First Name',
	 *     #   ],
	 *     #],
	 *   ]);
	 * ?>
	 * </code>'
	 *
	 * @param int $id The template ID.
	 * @param array{
	 *     title: string,
	 *     jsonFile: string,
	 *     thumbnailFile: string,
	 *     backThumbnailFile: string,
	 *     fields: array{key: string, value: string}
	 * } $fields Fields to update.
	 * @return array{
	 *   message: string,
	 *   data: array{
	 *     id: int,
	 *     orgId: int,
	 *     title: string,
	 *     templateUrl: string,
	 *     backTemplateUrl: string,
	 *     templateType: string,
	 *     thumbnailUrl: string,
	 *     backThumbnailUrl: string,
	 *     isShared: bool,
	 *     creator: array{id: int, fullName: string},
	 *     product: array,
	 *     envelopeType: array,
	 *     createdAt: string,
	 *     updatedAt: string }
	 * } The result of the request
	 * @throws OlcRequestError If the request fails.
	 * @throws InvalidConfigException
	 */

	public function update(int $id, array $fields): array {
		// Fetch template data
		$templateData = $this->get($id);

		$templateParams = [
			'title' => $templateData['data']['title'] ?? '',
			'templatePath' => UrlHelper::assetUrlToPath($templateData['data']['templateUrl'] ?? ''),
			'thumbnailPath' => UrlHelper::assetUrlToPath($templateData['data']['thumbnailUrl'] ?? ''),
			'backThumbnailPath' => UrlHelper::assetUrlToPath($templateData['data']['backThumbnailUrl'] ?? ''),
			'fields' => $templateData['data']['fields'] ?? [],
		];

		if (\array_key_exists('title', $fields)) {
			if (!is_string($fields['title']) || empty($fields['title'])) {
				throw new InvalidConfigException('The "title" parameter is required and should not be empty');
			}
			$templateParams['title'] = $fields['title'];
		}

		if (\array_key_exists('fields', $fields)) {
			if (!CustomFieldsHelper::validateList($fields['fields'])) {
				throw new InvalidConfigException('The "fields" parameter is required and should be a non-empty key-value pairs array');
			}
			$templateParams['fields'] = $fields['fields'];
		}

		if (
			array_key_exists('jsonFile', $fields)
			|| array_key_exists('thumbnailFile', $fields)
			|| array_key_exists('backThumbnailFile', $fields)
		) {
			$templateResponse = $this->upload($fields);
			$templateParams['templatePath'] = $templateResponse['data']['templatePath'];
			$templateParams['thumbnailPath'] = $templateResponse['data']['thumbnailPath'];
			$templateParams['backThumbnailPath'] = $templateResponse['data']['backThumbnailPath'];
		}

		$request = $this->getInstance()->getRequest();
		$response = $request->patch("/templates/$id", $templateParams, [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Get template against the given id
	 * <code>
	 * <?php
	 *   $response = $olc->templates()->get(1);
	 * ?>
	 * </code>
	 *
	 * @param int $id The template ID
	 * @return array{message: string, data: array} The response data.
	 * @throws OlcRequestError If the request fails.
	 */
	public function get(int $id): array {
		$request = $this->getInstance()->getRequest();
		$response = $request->get("/templates/$id", [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Get view proof against the given template id
	 * <code>
	 * <?php
	 *   $response = $olc->templates()->viewProof(1);
	 * ?>
	 * </code>
	 *
	 * @param int $id The template ID
	 * @return array{message: string, data: array} The response data.
	 * @throws OlcRequestError If the request fails.
	 * @todo Check for the correct response
	 */
	public function viewProof(int $id): array {
		$request = $this->getInstance()->getRequest();
		$response = $request->get("/templates/$id/view-proof", [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Remove a template by the given template id
	 * <code>
	 * <?php
	 *   $response = $olc->templates()->delete(1);
	 * ?>
	 * </code>
	 *
	 * @param int $id The template ID
	 * @return array{message: string, data: array} The response data.
	 * @throws OlcRequestError If the request fails.
	 * @todo Check for the correct response
	 */
	public function delete(int $id): array {
		$request = $this->getInstance()->getRequest();
		$response = $request->delete("/templates/$id", [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Duplicates a template by the given id
	 * <code>
	 * <?php
	 *   $response = $olc->templates()->duplicate(1);
	 * ?>
	 * </code>
	 *
	 * @param int $id The template ID
	 * @return array{message: string, data: array} The response data.
	 * @throws OlcRequestError If the request fails.
	 * @todo Check for the correct response
	 */
	public function duplicate(int $id): array {
		$request = $this->getInstance()->getRequest();
		$response = $request->post("/templates/$id/duplicate", [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Upload an image file to cloud storage and returns the absolute image url
	 * <code>
	 * <?php
	 *   $fileUrl = $olc->templates()->uploadFile('path/to/file.jpg');
	 * ?>
	 * </code>
	 *
	 * @param string $filePath File path to the image
	 * @return string The uploaded image url
	 * @throws OlcRequestError If the request fails.
	 * @throws NotFoundException
	 */
	public function uploadFile(string $filePath): string {
		if (!\is_file($filePath)) {
			throw new NotFoundException('The given file path is not valid');
		}

		$request = $this->getInstance()->getRequest();
		$response = $request->post('/templates/uploadFile', null, [
			'authorize' => true,
			'jsonHeaders' => false,
			'client' => [
				'multipart' => [
					[
						'name' => 'image',
						'filename' => FileHelper::toRandomName($filePath),
						'contents' => Utils::tryFopen($filePath, 'r'),
					],
				]
			]
		]);

		return $request->retrieveValueByPath($response, 'data[filePath]');
	}

	/**
	 * Upload a template to the cloud storage
	 * <code>
	 * <?php
	 *   $fileUrl = $olc->templates()->upload([
	 *     'jsonFile' => 'path/to/template.json',
	 *     'thumbnailFile' => 'path/to/thumbnail.jpg',
	 *     'backThumbnailFile' => 'path/to/back-thumbnail.jpg',
	 *   ]);
	 * ?>
	 * </code>
	 * @param array{jsonFile: string, thumbnailFile: string, backThumbnailFile: string} $params Parameters to send with the request.
	 * @return array{
	 *   message: string,
	 *   data: array{
	 *     templatePath: string,
	 *     thumbnailPath: string,
	 *     backTemplatePath: string }
	 * } The result of the request
	 * @throws InvalidConfigException
	 * @throws OlcRequestError If the request fails.
	 */
	public function upload(array $params): array {
		//<editor-fold desc="Params basic validation">
		if (!\array_key_exists('jsonFile', $params)
			|| !\is_string($params['jsonFile']) || !\is_file($params['jsonFile'])) {
			throw new InvalidConfigException('The "jsonFile" parameter is required and should be a valid path');
		}

		if (!\array_key_exists('thumbnailFile', $params)
			|| !\is_string($params['thumbnailFile']) || !\is_file($params['thumbnailFile'])) {
			throw new InvalidConfigException('The "thumbnailFile" parameter is required and should be a valid path');
		}

		if (!\array_key_exists('backThumbnailFile', $params) ||
			!\is_string($params['backThumbnailFile']) || !\is_file($params['backThumbnailFile'])) {
			throw new InvalidConfigException('The "backThumbnailFile" parameter is required and should be a valid path');
		}
		//</editor-fold>

		$request = $this->getInstance()->getRequest();

		$response = $request->post('/templates/upload', null, [
			'authorize' => true,
			'jsonHeaders' => false,
			'client' => [
				'multipart' => [
					[
						'name' => 'json',
						'filename' => FileHelper::toRandomName($params['jsonFile']),
						'contents' => Utils::tryFopen($params['jsonFile'], 'r'),
					],
					[
						'name' => 'thumbnail',
						'filename' => FileHelper::toRandomName($params['thumbnailFile']),
						'contents' => Utils::tryFopen($params['thumbnailFile'], 'r'),
					],
					[
						'name' => 'backThumbnail',
						'filename' => FileHelper::toRandomName($params['backThumbnailFile']),
						'contents' => Utils::tryFopen($params['backThumbnailFile'], 'r'),
					],
				]
			]
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Fetch all template categories
	 * <code>
	 * <?php
	 *   $response = $olc->templates()->categories();
	 * ?>
	 * </code>
	 *
	 * @return array{message: string, data: array} The response data.
	 * @throws OlcRequestError If the request fails.
	 */
	public function categories(): array {
		$request = $this->getInstance()->getRequest();
		$response = $request->get('/templates/categories', [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Fetch all templates by the given category id
	 * <code>
	 * <?php
	 *   //
	 *   $response = $olc->templates()->allByCategory();
	 * ?>
	 * @param array{categoryIds?: int, page?: int, pageSize?: int} $params Parameters to send with the request.
	 * </code>
	 *
	 * @return array{message: string, data: array} The response data.
	 * @throws OlcRequestError If the request fails.
	 */
	public function allByCategory(array $params = []): array {
		$request = $this->getInstance()->getRequest();
		$query = \count($params) ? '?' . \http_build_query($params) : '';
		$response = $request->get('/templates/by-tab' . $query, [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}
}
