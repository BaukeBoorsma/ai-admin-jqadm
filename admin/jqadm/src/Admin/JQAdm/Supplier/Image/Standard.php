<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 * @package Admin
 * @subpackage JQAdm
 */


namespace Aimeos\Admin\JQAdm\Supplier\Image;

sprintf( 'image' ); // for translation


/**
 * Default implementation of supplier image JQAdm client.
 *
 * @package Admin
 * @subpackage JQAdm
 */
class Standard
	extends \Aimeos\Admin\JQAdm\Common\Admin\Factory\Base
	implements \Aimeos\Admin\JQAdm\Common\Admin\Factory\Iface
{
	/** admin/jqadm/supplier/image/name
	 * Name of the image subpart used by the JQAdm supplier implementation
	 *
	 * Use "Myname" if your class is named "\Aimeos\Admin\Jqadm\Supplier\Image\Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the JQAdm class name
	 * @since 2017.07
	 * @category Developer
	 */


	/**
	 * Copies a resource
	 *
	 * @return string HTML output
	 */
	public function copy()
	{
		$view = $this->addViewData( $this->getView() );

		$view->imageData = $this->toArray( $view->item, true );
		$view->imageBody = '';

		foreach( $this->getSubClients() as $client ) {
			$view->imageBody .= $client->copy();
		}

		return $this->render( $view );
	}


	/**
	 * Creates a new resource
	 *
	 * @return string HTML output
	 */
	public function create()
	{
		$view = $this->addViewData( $this->getView() );
		$siteid = $this->getContext()->getLocale()->getSiteId();

		$itemData = $this->toArray( $view->item );
		$data = array_replace_recursive( $itemData, $view->param( 'image', [] ) );

		foreach( $data as $idx => $entry )
		{
			$data[$idx]['media.siteid'] = $siteid;
			$data[$idx]['media.url'] = $itemData[$idx]['media.url'];
			$data[$idx]['media.preview'] = $itemData[$idx]['media.preview'];
			$data[$idx]['supplier.lists.siteid'] = $siteid;
		}

		$view->imageData = $data;
		$view->imageBody = '';

		foreach( $this->getSubClients() as $client ) {
			$view->imageBody .= $client->create();
		}

		return $this->render( $view );
	}


	/**
	 * Deletes a resource
	 */
	public function delete()
	{
		parent::delete();

		$item = $this->getView()->item;
		$cntl = \Aimeos\Controller\Common\Media\Factory::createController( $this->getContext() );

		foreach( $item->getListItems( 'media', null, null, false ) as $listItem )
		{
			if( ( $refItem = $listItem->getRefItem() ) !== null ) {
				$cntl->delete( $refItem );
			}

			$item->deleteListItem( 'media', $listItem, $refItem );
		}
	}


	/**
	 * Returns a single resource
	 *
	 * @return string HTML output
	 */
	public function get()
	{
		$view = $this->addViewData( $this->getView() );

		$view->imageData = $this->toArray( $view->item );
		$view->imageBody = '';

		foreach( $this->getSubClients() as $client ) {
			$view->imageBody .= $client->get();
		}

		return $this->render( $view );
	}


	/**
	 * Saves the data
	 */
	public function save()
	{
		$view = $this->getView();

		try
		{
			$view->item = $this->fromArray( $view->item, $view->param( 'image', [] ) );
			$view->imageBody = '';

			foreach( $this->getSubClients() as $client ) {
				$view->imageBody .= $client->save();
			}

			return;
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			$error = array( 'supplier-item-image' => $this->getContext()->getI18n()->dt( 'mshop', $e->getMessage() ) );
			$view->errors = $view->get( 'errors', [] ) + $error;
			$this->logException( $e );
		}
		catch( \Exception $e )
		{
			$error = array( 'supplier-item-image' => $e->getMessage() . ', ' . $e->getFile() . ':' . $e->getLine() );
			$view->errors = $view->get( 'errors', [] ) + $error;
			$this->logException( $e );
		}

		throw new \Aimeos\Admin\JQAdm\Exception();
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Admin\JQAdm\Iface Sub-client object
	 */
	public function getSubClient( $type, $name = null )
	{
		/** admin/jqadm/supplier/image/decorators/excludes
		 * Excludes decorators added by the "common" option from the supplier JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "admin/jqadm/common/decorators/default" before they are wrapped
		 * around the JQAdm client.
		 *
		 *  admin/jqadm/supplier/image/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Admin\JQAdm\Common\Decorator\*") added via
		 * "admin/jqadm/common/decorators/default" to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2017.07
		 * @category Developer
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/supplier/image/decorators/global
		 * @see admin/jqadm/supplier/image/decorators/local
		 */

		/** admin/jqadm/supplier/image/decorators/global
		 * Adds a list of globally available decorators only to the supplier JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Admin\JQAdm\Common\Decorator\*") around the JQAdm client.
		 *
		 *  admin/jqadm/supplier/image/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Admin\JQAdm\Common\Decorator\Decorator1" only to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2017.07
		 * @category Developer
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/supplier/image/decorators/excludes
		 * @see admin/jqadm/supplier/image/decorators/local
		 */

		/** admin/jqadm/supplier/image/decorators/local
		 * Adds a list of local decorators only to the supplier JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Admin\JQAdm\Supplier\Decorator\*") around the JQAdm client.
		 *
		 *  admin/jqadm/supplier/image/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Admin\JQAdm\Supplier\Decorator\Decorator2" only to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2017.07
		 * @category Developer
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/supplier/image/decorators/excludes
		 * @see admin/jqadm/supplier/image/decorators/global
		 */
		return $this->createSubClient( 'supplier/image/' . $type, $name );
	}


	/**
	 * Adds the required data used in the supplier template
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @return \Aimeos\MW\View\Iface View object with assigned parameters
	 */
	protected function addViewData( \Aimeos\MW\View\Iface $view )
	{
		$context = $this->getContext();

		$typeManager = \Aimeos\MShop::create( $this->getContext(), 'media/type' );
		$listTypeManager = \Aimeos\MShop::create( $this->getContext(), 'supplier/lists/type' );

		$search = $typeManager->createSearch()->setSlice( 0, 0x7fffffff );
		$search->setConditions( $search->compare( '==', 'media.type.domain', 'supplier' ) );
		$search->setSortations( array( $search->sort( '+', 'media.type.label' ) ) );

		$listSearch = $listTypeManager->createSearch( true )->setSlice( 0, 0x7fffffff );
		$listSearch->setConditions( $listSearch->compare( '==', 'supplier.lists.type.domain', 'media' ) );
		$listSearch->setSortations( array( $listSearch->sort( '+', 'supplier.lists.type.label' ) ) );

		$view->imageListTypes = $this->sortType( $listTypeManager->searchItems( $listSearch ) );
		$view->imageTypes = $typeManager->searchItems( $search );

		return $view;
	}


	/**
	 * Removes the media reference and the media item if not shared
	 *
	 * @param \Aimeos\MShop\Supplier\Item\Iface $item Supplier item including media reference
	 * @param array $listItems Media list items to be removed
	 * @return \Aimeos\MShop\Supplier\Item\Iface Modified supplier item
	 */
	protected function deleteMediaItems( \Aimeos\MShop\Supplier\Item\Iface $item, array $listItems )
	{
		$context = $this->getContext();
		$cntl = \Aimeos\Controller\Common\Media\Factory::createController( $context );
		$manager = \Aimeos\MShop::create( $context, 'supplier' );
		$search = $manager->createSearch();

		foreach( $listItems as $listItem )
		{
			$expr = [
				$search->compare( '==', 'supplier.lists.domain', 'media' ),
				$search->compare( '==', 'supplier.lists.type', $listItem->getType() ),
				$search->compare( '==', 'supplier.lists.refid', $listItem->getRefId() ),
			];
			$search->setConditions( $search->combine( '&&', $expr ) );
			$items = $manager->searchItems( $search );
			$refItem = null;

			if( count( $items ) === 1 && ( $refItem = $listItem->getRefItem() ) !== null ) {
				$cntl->delete( $refItem );
			}

			$item->deleteListItem( 'media', $listItem, $refItem );
		}

		return $item;
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of JQAdm client names
	 */
	protected function getSubClientNames()
	{
		/** admin/jqadm/supplier/image/standard/subparts
		 * List of JQAdm sub-clients rendered within the supplier image section
		 *
		 * The output of the frontend is composed of the code generated by the JQAdm
		 * clients. Each JQAdm client can consist of serveral (or none) sub-clients
		 * that are responsible for rendering certain sub-parts of the output. The
		 * sub-clients can contain JQAdm clients themselves and therefore a
		 * hierarchical tree of JQAdm clients is composed. Each JQAdm client creates
		 * the output that is placed inside the container of its parent.
		 *
		 * At first, always the JQAdm code generated by the parent is printed, then
		 * the JQAdm code of its sub-clients. The order of the JQAdm sub-clients
		 * determines the order of the output of these sub-clients inside the parent
		 * container. If the configured list of clients is
		 *
		 *  array( "subclient1", "subclient2" )
		 *
		 * you can easily change the order of the output by reordering the subparts:
		 *
		 *  admin/jqadm/<clients>/subparts = array( "subclient1", "subclient2" )
		 *
		 * You can also remove one or more parts if they shouldn't be rendered:
		 *
		 *  admin/jqadm/<clients>/subparts = array( "subclient1" )
		 *
		 * As the clients only generates structural JQAdm, the layout defined via CSS
		 * should support adding, removing or reordering content by a fluid like
		 * design.
		 *
		 * @param array List of sub-client names
		 * @since 2017.07
		 * @category Developer
		 */
		return $this->getContext()->getConfig()->get( 'admin/jqadm/supplier/image/standard/subparts', [] );
	}


	/**
	 * Creates new and updates existing items using the data array
	 *
	 * @param \Aimeos\MShop\Supplier\Item\Iface $item Supplier item object without referenced domain items
	 * @param string[] $data Data array
	 */
	protected function fromArray( \Aimeos\MShop\Supplier\Item\Iface $item, array $data )
	{
		$context = $this->getContext();

		$mediaManager = \Aimeos\MShop::create( $context, 'media' );
		$listManager = \Aimeos\MShop::create( $context, 'supplier/lists' );
		$cntl = \Aimeos\Controller\Common\Media\Factory::createController( $context );

		$listItems = $item->getListItems( 'media', null, null, false );
		$files = (array) $this->getView()->request()->getUploadedFiles();

		foreach( $data as $idx => $entry )
		{
			if( ( $listItem = $item->getListItem( 'media', $entry['supplier.lists.type'], $entry['media.id'], false ) ) === null ) {
				$listItem = $listManager->createItem();
			}

			if( ( $refItem = $listItem->getRefItem() ) === null ) {
				$refItem = $mediaManager->createItem();
			}

			$refItem->fromArray( $entry );

			if( ( $file = $this->getValue( $files, 'image/' . $idx . '/file' ) ) !== null && $file->getError() !== UPLOAD_ERR_NO_FILE )
			{
				$refItem->getId() ?: $refItem->setUrl( '' )->setPreview( '' ); // keep copied image
				$cntl->add( $refItem, $file );
			}

			$conf = [];

			foreach( (array) $this->getValue( $entry, 'config/key' ) as $num => $key )
			{
				if( trim( $key ) !== '' && ( $val = $this->getValue( $entry, 'config/val/' . $num ) ) !== null ) {
					$conf[$key] = trim( $val );
				}
			}

			$listItem->fromArray( $entry );
			$listItem->setPosition( $idx );
			$listItem->setConfig( $conf );

			$item->addListItem( 'media', $listItem, $refItem );

			unset( $listItems[$listItem->getId()] );
		}

		return $this->deleteMediaItems( $item, $listItems );
	}


	/**
	 * Constructs the data array for the view from the given item
	 *
	 * @param \Aimeos\MShop\Supplier\Item\Iface $item Supplier item object including referenced domain items
	 * @param boolean $copy True if items should be copied, false if not
	 * @return string[] Multi-dimensional associative list of item data
	 */
	protected function toArray( \Aimeos\MShop\Supplier\Item\Iface $item, $copy = false )
	{
		$data = [];
		$siteId = $this->getContext()->getLocale()->getSiteId();

		foreach( $item->getListItems( 'media', null, null, false ) as $listItem )
		{
			if( ( $refItem = $listItem->getRefItem() ) === null ) {
				continue;
			}

			$list = $listItem->toArray( true ) + $refItem->toArray( true );

			if( $copy === true )
			{
				$list['supplier.lists.siteid'] = $siteId;
				$list['supplier.lists.id'] = '';
				$list['media.siteid'] = $siteId;
				$list['media.id'] = null;
			}

			$list['supplier.lists.datestart'] = str_replace( ' ', 'T', $list['supplier.lists.datestart'] );
			$list['supplier.lists.dateend'] = str_replace( ' ', 'T', $list['supplier.lists.dateend'] );

			foreach( $list['supplier.lists.config'] as $key => $val )
			{
				$list['config']['key'][] = $key;
				$list['config']['val'][] = $val;
			}

			$data[] = $list;
		}

		return $data;
	}


	/**
	 * Returns the rendered template including the view data
	 *
	 * @param \Aimeos\MW\View\Iface $view View object with data assigned
	 * @return string HTML output
	 */
	protected function render( \Aimeos\MW\View\Iface $view )
	{
		/** admin/jqadm/supplier/image/template-item
		 * Relative path to the HTML body template of the image subpart for suppliers.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the result shown in the body of the frontend. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in admin/jqadm/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "default" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "default"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating the HTML code
		 * @since 2017.07
		 * @category Developer
		 */
		$tplconf = 'admin/jqadm/supplier/image/template-item';
		$default = 'supplier/item-image-standard';

		return $view->render( $view->config( $tplconf, $default ) );
	}
}