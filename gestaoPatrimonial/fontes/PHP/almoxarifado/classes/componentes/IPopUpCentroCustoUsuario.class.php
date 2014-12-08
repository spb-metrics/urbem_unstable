<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
* Arquivo de popup de busca de Centro de Custo que o usuário tem permissão
* Data de Criação: 35/06/2006

* @author Analista: Diego Victoria
* @author Desenvolvedor: Leandro André Zis

* @package URBEM
* @subpackage

* Casos de uso: uc-03.03.07
                uc-03.04.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once ( CLA_BUSCAINNER );

class  IPopUpCentroCustoUsuario extends BuscaInner
{
    /**
        * @access Private
        * @var Object
    */

    public $obForm;

    /**
        * Metodo Construtor
        * @access Public

    */

    public function IPopUpCentroCustoUsuario($obForm)
    {
        parent::BuscaInner();

        $this->obForm = $obForm;

        $this->setRotulo                 ( 'Centro de Custo'              );
        $this->setTitle                  ( 'Informe o centro de custo.'                 );
        $this->setId                     ( 'stNomCentroCusto'  );
        $this->setNull                   ( false              );

        $this->obCampoCod->setName       ( "inCodCentroCusto" );
        $this->obCampoCod->setSize       ( 10                  );
        $this->obCampoCod->setMaxLength  ( 10                 );
        $this->obCampoCod->setAlign      ( "left"             );

    }

    public function montaHTML()
    {

        $this->setFuncaoBusca("abrePopUp('" . CAM_GP_ALM_POPUPS . "centroCusto/FLManterCentroCusto.php','".$this->obForm->getName()."', '". $this->obCampoCod->stName ."','". $this->stId . "','','" . Sessao::getId() .
"&usuario=1','800','550');");

     $this->obCampoCod->obEvento->setOnChange("ajaxJavaScript( '".CAM_GP_ALM_PROCESSAMENTO.'OCCentroCustoUsuario.php?'.Sessao::getId()."&usuario=1&nomCampoUnidade=".$this->obCampoCod->getName()."&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->stId."&stNomForm=".$this->obForm->getName()."&inCodigo='+this.value, 'buscaPopup' );");

       parent::montaHTML();
    }
}
?>
