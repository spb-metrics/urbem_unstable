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
* Página de Listagem da Objeto
* Data de Criação   : 04/07/2007

* @author Analista: Diego Victoria
* @author Desenvolvedor: Leandro André Zis

* @ignore

* $Id: LSProcurarContrato.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso :uc-03.04.07, uc-03.04.05
*/

/*
$Log$
Revision 1.1  2006/10/11 17:21:12  domluc
p/ Diegon:
   O componente de Contrato gera no formulario que o chama um buscainner e um span, o buscainner somente aceita preenchimento via PopUp, ou seja, não é possivel digitar diretamente o numero do contrato.
   Chamando a popup do buscainner, ele devera poder filtrar por ( em ordem)
1) Número do Contrato ( inteiro)
2) Exercicio ( ref a Contrato) ( componente exercicio)
3) Modalidade ( combo)
4) Codigo da Licitação  ( inteiro )
5) Entidade ( componente)

entao o usuario clica em Ok, e o sistema exibe uma lista correspondente ao filtro informado.
o usuario seleciona um dos contratos na listageme o sistema fecha a popup, retornando ao formulario, onde o sistema preenche o numero do convenio e no span criado pelo componente , exibe as informações recorrentes, que sao:
- exercicio
- modalidade
- licitação
- entidade
- cgm contratado

era isso

Revision 1.4  2006/10/04 08:53:22  cleisson
novo componente IPopUpObjeto

Revision 1.3  2006/07/06 14:05:54  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:11:10  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_LIC_MAPEAMENTO . 'TLicitacaoContrato.class.php' );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarObjeto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

  $stFncJavaScript .= " function insereObjeto(num,nom) {  \n";
  $stFncJavaScript .= " var sNum;                  \n";
  $stFncJavaScript .= " var sNom;                  \n";
  $stFncJavaScript .= " sNum = num;                \n";
  $stFncJavaScript .= " sNom = nom;                \n";
  $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; \n";
  $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"  ].".value = sNum; \n";
  $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"  ].".focus(); \n";
  $stFncJavaScript .= " window.close();            \n";
  $stFncJavaScript .= " }                          \n";

$stCaminho = CAM_GP_COM_INSTANCIAS."objeto/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
    DEFAULT       : $pgProx = $pgForm;
}

$stLink = "&stAcao=".$stAcao;

$filtro = Sessao::read('filtro');
if ($_REQUEST['stHdnDescricao'] || $_REQUEST['inCodEntidade']) {
    foreach ($_REQUEST as $key => $value) {
        $filtro[$key] = $value;
    }
} else {
    if ($filtro) {
        foreach ($filtro as $key => $value) {
            $_REQUEST[$key] = $value;
        }
    }
    Sessao::write('paginando', true);
}
Sessao::write('filtro', $filtro);

$obTLicitacaoContrato = new TLicitacaoContrato;

if ( ( $_POST['stDataInicial']) ) {
    $stFiltro .= " and contrato.dt_assinatura >= to_date( '" . $_POST['stDataInicial'] . "', 'dd/mm/yyyy')  ";
}

if ($_POST['stDataFinal']) {
    $stFiltro .= " and contrato.dt_assinatura <= to_date( '" .$_POST['stDataFinal'] . "' ,'dd/mm/yyyy' )";
}

$obTLicitacaoContrato->setDado ( 'cod_entidade', $_POST['inCodEntidade'] );

$obTLicitacaoContrato->recuperaRelacionamento ( $rsLista, $stFiltro );
$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->setTitulo("Contratos");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data Assinatura" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Objeto" );
$obLista->ultimoCabecalho->setWidth( 70 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "num_contrato" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "dt_assinatura" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereObjeto();" );
$obLista->ultimaAcao->addCampo("1","num_contrato");
$obLista->ultimaAcao->addCampo("2","descricao");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();

?>
