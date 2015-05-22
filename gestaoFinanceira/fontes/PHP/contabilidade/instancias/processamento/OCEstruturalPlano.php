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
* Arquivo instância para popup de CGM
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

* $Id: OCEstruturalPlano.php 62511 2015-05-15 17:45:15Z evandro $

Casos de uso: uc-02.02.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php");

function buscaPopup()
{
    if ($_GET[$_GET['stNomCampoCod']]) {

        isset($_REQUEST['stExercicio']) ? $stExercicio = $_REQUEST['stExercicio'] : Sessao::getExercicio();
            
        $obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;
        $obRContabilidadePlanoContaAnalitica->setCodEstrutural( $_GET[$_GET['stNomCampoCod']] );
        $obRContabilidadePlanoContaAnalitica->setExercicio( $stExercicio );
        $obRContabilidadePlanoContaAnalitica->consultar();
        $stDescricao = $obRContabilidadePlanoContaAnalitica->getNomConta();
    }
    $stJs .= "retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', 'frm', '".$stDescricao."')";

    return $stJs;
}
switch ($_GET['stCtrl']) {
    case 'buscaPopup':
        $stJs .= buscaPopup();
    break;
}
if ($stJs) {
    echo $stJs;
}
?>
