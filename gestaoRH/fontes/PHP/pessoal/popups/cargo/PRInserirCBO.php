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
* Arquivo instância para popup para inserir CBO
* Data de Criação: 13/06/2013
* @author Desenvolvedor: Evandro Melos
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCbo.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "InserirCBO";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obTPessoalCBO = new TPessoalCbo;
$obErro = new Erro;

$obTPessoalCBO->setDado('codigo',$_POST['stNumCbo']);
$obTPessoalCBO->setDado('descricao',$_POST['stNomeCbo']);
$obTPessoalCBO->setDado('dt_inicial',$_POST['dtInicial']);
$obTPessoalCBO->setDado('dt_final',$_POST['dtFinal']);

$obErro = $obTPessoalCBO->recuperaTodos($rsRecord, " WHERE codigo = ".$_POST['stNumCbo']." and descricao ilike '".$_POST['stNomeCbo']."'");

if ($rsRecord->getNumLinhas() < 1) {
    $obErro = $obTPessoalCBO->inclusao();
    if ($obErro->ocorreu()) {
        SistemaLegado::alertaAviso($pgForm,$obErro->getDescricao(),'form','erro', Sessao::getId(),'');
    } else {
        $stJs = "
        window.parent.window.opener.document.frm.inNumCBO.value = '".$_POST['stNumCbo']."';
        window.parent.window.opener.document.getElementById('inNomCBO').innerHTML = '".$_POST['stNomeCbo']."';
        window.parent.close();
        ";
        SistemaLegado::executaFrameOculto($stJs);
    }
} else {
    SistemaLegado::alertaAviso($pgForm,"Registro já cadastrado!",'form','erro', Sessao::getId(),'');
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
