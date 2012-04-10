<?php
//$Copyright$

/**
 * Original uninstall.php file
 * @package   Zoo Component
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) 2007 - 2009 YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$error = false;
$db =& JFactory::getDBO();

// additional extensions
$j16 = version_compare(JVERSION, '1.6.0', 'ge');

if ($j16) {
        $add = $this->manifest->xpath('additional');
        if ($add) $add = $add[0];
} else {
        $add = $this->manifest->getElementByPath('additional');
}

$extensions = array();

if ((is_a($add, 'JSimpleXMLElement') || is_a($add, 'JXMLElement')) && count($add->children())) {
        $exts =& $add->children();

        foreach ($exts as $ext) {
                $extensions[] = get_extension($ext, $db, $j16);
        }
}

// uninstall additional extensions
for ($i = 0; $i < count($extensions); $i++) {
	$extension =& $extensions[$i];
        
	if ($extension['id'] > 0 && $extension['installer']->uninstall($extension['type'], $extension['id'], $extension['client_id'])) {
		$extension['status'] = true;
	}
}

function attr($ext, $name, $j16) {
        return $j16 ? $ext->getAttribute($name) : $ext->attributes($name);
}

function get_extension($ext, $db, $j16) {
        $type = $ext->name();
        $folder = attr($ext, 'type', $j16);
        $attribute_name = attr($ext, 'name', $j16);

        if ($j16) {
                $query = 'SELECT * FROM #__extensions WHERE type='.$db->Quote($type).' AND element='.$db->Quote($attribute_name);

                if ($type == 'plugin') {
                        $query .= ' AND folder='.$db->Quote($folder);
                }
        } else {
                $query = 'SELECT * FROM #__'.$type.'s WHERE ';
                
                if ($type == 'plugin') {
                        $query .= 'element='.$db->Quote($attribute_name).' AND folder='.$db->Quote($folder);
                } else if ($type == 'module') {
                        $query .= 'module='.$db->Quote($attribute_name);
                }
        }

        $db->setQuery($query);
        $res = $db->loadObject();

        $idAttr = $j16 ? 'extension_id' : 'id';
        $idVal = $res->$idAttr;

        return array(
                'element' => $res->element,
                'name' => $ext->data(),
                'type' => $type,
                'id' => isset($idVal) ? $idVal : 0,
                'client_id' => isset($res->client_id) ? $res->client_id : 0,
                'installer' => new JInstaller(),
                'status' => false
        );
}

?>
<h3><?php echo JText::_('Additional Extensions'); ?></h3>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title"><?php echo JText::_('Extension'); ?></th>
			<th width="60%"><?php echo JText::_('Status'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach ($extensions as $i => $ext) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="key"><?php echo $ext['name']; ?> (<?php echo JText::_($ext['type']); ?>)</td>
				<td>
					<?php $style = $ext['status'] ? 'font-weight: bold; color: green;' : 'font-weight: bold; color: red;'; ?>
					<span style="<?php echo $style; ?>"><?php echo $ext['status'] ? JText::_('Uninstalled successfully') : JText::_('Uninstall FAILED'); ?></span>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>