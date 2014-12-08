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
* Popup para inclusão de arquivos anexos aos processo
* Data de Criação: 17/10/2006

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 17525 $
$Name$
$Author: cassiano $
$Date: 2006-11-09 13:44:15 -0200 (Qui, 09 Nov 2006) $

Casos de uso: uc-01.06.98
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once(CAM_GA_PROT_MAPEAMENTO."TPROCopiaDigital.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "DocumentoProcesso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId();
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
$obTPROCopiaDigital = new TPROCopiaDigital();
Sessao::write('nom_arquivo',$_FILES['stArquivo']['name']);

if ($_FILES['stArquivo']['type'] != 'image/jpeg' and $_POST['boImagem'] == 't') {
    SistemaLegado::exibeAviso("O Arquivo precisa ser estar no formato JPG!","","erro");
} elseif ($_FILES['stArquivo']['size'] > 1000000) {
    SistemaLegado::exibeAviso("O Arquivo não pode ter mais que 1000KB","","erro");
} else {
    //MONTA O NOME DO DIRETORIO TEMPORARIO UTILIZANDO O ID DA SESSÃO
    $inPosInicial = strpos(Sessao::getId(),'=') + 1;
    $inPosFinal = strpos(Sessao::getId(),'&') - $inPosInicial;
    $stIdSessao = substr(Sessao::getId(),$inPosInicial,$inPosFinal );
    $stDiretorioSessao = CAM_PROTOCOLO."tmp/".$stIdSessao;
    if ( !is_dir($stDiretorioSessao) ) {
        mkdir($stDiretorioSessao,0755);//CRIA O DIRETORIO
    }
    $stDiretorioDocumento = $_POST['inCodigoDocumento'].'_'.(int) Sessao::read('codigo_processo');
    $stDiretorioDocumento .= "_".Sessao::getExercicio();
    if ( !is_dir($stDiretorioSessao."/".$stDiretorioDocumento) ) {
        mkdir($stDiretorioSessao."/".$stDiretorioDocumento,0755);
    }
    if ( !is_file( $stDiretorioSessao."/".$stDiretorioDocumento."/".$_FILES['stArquivo']['name'] ) ) {
        $boCopia = copy( $_FILES['stArquivo']['tmp_name'], $stDiretorioSessao."/".$stDiretorioDocumento."/".$_FILES['stArquivo']['name'] );
        chmod($stDiretorioSessao."/".$stDiretorioDocumento."/".$_FILES['stArquivo']['name'],0777);
        if ($boCopia) {
            SistemaLegado::exibeAvisoTelaPrincipal("Arquivo enviado com sucesso!","","");
        } else {
            SistemaLegado::exibeAviso("Erro no upload de arquivo!","","erro");
        }
    } else {
        SistemaLegado::exibeAviso("O arquivo enviado já existe no servidor, renomeie o arquivo e envie novamente!","","erro");
    }
}

?>
