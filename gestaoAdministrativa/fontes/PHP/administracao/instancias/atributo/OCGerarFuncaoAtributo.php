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
* Arquivo de instância para manutenção de atributos
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3347 $
$Name$
$Author: pablo $
$Date: 2005-12-05 11:05:04 -0200 (Seg, 05 Dez 2005) $

Casos de uso: uc-01.03.96
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_REGRA."RCadastroDinamico.class.php");
include_once '../../../bibliotecas/mascaras.lib.php';

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$rsAtributosDisponiveis = $rsAtributosSelecionados = $rsAtributo = $rsAtributoCompras = new RecordSet;
$obRRegra = new RCadastroDinamico;
$obRRegra->obRModulo->setCodModulo( $_REQUEST['inCodModulo'] );
$obRRegra->verificaModulo();
//$obRRegra->recuperaTodosCadastro($rsAtributoCadastro);

// Acoes por pagina
switch ($stCtrl) {
    case "ComboCadastro":
        $obRRegra->setCodCadastro( ($_POST['inCodCadastro'])?$_POST['inCodCadastro']:'0' );
        $obRRegra->recuperaAtributoFuncao( $rsAtributoComFuncao, $rsAtributoSemFuncao );

        //Combo Disponíveis
        $stCombo = 'inCodAtributosDisponiveis';
        $js .= "limpaSelect(f.$stCombo,0); \n";
        $inCount = 0;
        while (!$rsAtributoSemFuncao->eof()) {
            $inCodigo   = $rsAtributoSemFuncao->getCampo("cod_atributo");
            $stNome     = $rsAtributoSemFuncao->getCampo("nom_atributo");
            $js .= "f.$stCombo.options[$inCount] = new Option('".$stNome."','".$inCodigo."'); \n";
            $rsAtributoSemFuncao->proximo();
            $inCount++;
        }

        //Combo Selecionados
        $stCombo = 'inCodAtributosSelecionados';
        $js .= "limpaSelect(f.$stCombo,0); \n";
        $inCount = 0;
        while (!$rsAtributoComFuncao->eof()) {
            $inCodigo   = $rsAtributoComFuncao->getCampo("cod_atributo");
            $stNome     = $rsAtributoComFuncao->getCampo("nom_atributo");
            $js .= "f.$stCombo.options[$inCount] = new Option('".$stNome."','".$inCodigo."'); \n";
            $rsAtributoComFuncao->proximo();
            $inCount++;
        }
        $js .= "f.Ok.disabled = false;\n";
    break;
    case "Limpar":
        $js .= "limpaSelect(f.inCodAtributosDisponiveis,0);\n";
        $js .= "limpaSelect(f.inCodAtributosSelecionados,0);";
    break;

}
if($js)
    executaFrameOculto($js);
?>
