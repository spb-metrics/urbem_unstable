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
    * Data de Criação: 21/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 26149 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-10-17 11:28:05 -0200 (Qua, 17 Out 2007) $

    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.4  2007/10/17 13:27:03  hboaventura
correção dos arquivos

Revision 1.3  2007/10/05 15:24:32  hboaventura
inclusão dos arquivos

Revision 1.2  2007/10/05 12:59:35  hboaventura
inclusão dos arquivos

Revision 1.1  2007/09/27 12:57:24  hboaventura
adicionando arquivos

Revision 1.1  2007/09/18 15:11:04  hboaventura
Adicionando ao repositório

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemBaixado.class.php" );
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php" );

$stPrograma = "ManterBaixarBem";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTPatrimonioBemBaixado = new TPatrimonioBemBaixado();
$obTPatrimonioBem = new TPatrimonioBem();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioBemBaixado );;

switch ($stAcao) {
    case 'incluir':
        //verifica se existe pelo menos um bem a ser baixado
        $arBem = Sessao::read('bens');
        if ( count( $arBem ) == 0 ) {
            $stMensagem = "Você precisa baixar pelo menos 1 bem.";
        } else {
            if ( implode('',array_reverse(explode('/',$_REQUEST['dtBaixa']))) > date('Ymd') ) {
                $stMensagem = "A data de baixa deve ser menor ou igual a data de hoje";
            } else {
                foreach ($arBem as $arTEMP) {
                    $obTPatrimonioBem->setDado( 'cod_bem', $arTEMP['cod_bem'] );
                    $obTPatrimonioBem->recuperaPorChave( $rsBem );
                    if ( implode('',array_reverse(explode('/',$rsBem->getCampo('dt_aquisicao')))) >  implode('',array_reverse(explode('/',$_REQUEST['dtBaixa']))) ) {
                        $arBensInvalidos[] = $rsBem->getCampo('cod_bem');
                    }
                }
                if ( count( $arBensInvalidos ) > 1 ) {
                    $stMensagem = 'O(s) Bem(s) '.implode(',',$arBensInvalidos).' não foram baixados porque a data de aquisição é superior a data de baixa';
                } elseif ( count( $arBensInvalidos ) == 1 ) {
                    $stMensagem = 'O Bem '.implode(',',$arBensInvalidos).' não foi baixado porque a data de aquisição é superior a data de baixa';
                }
            }
        }
        if (!$stMensagem) {
            //seta os dados e inclui
            $obTPatrimonioBemBaixado->setDado( 'dt_baixa', $_REQUEST['dtBaixa'] );
            $obTPatrimonioBemBaixado->setDado( 'motivo', $_REQUEST['stMotivo'] );
            foreach ($arBem as $arTEMP) {
                $obTPatrimonioBemBaixado->setDado( 'cod_bem', $arTEMP['cod_bem'] );
                $obTPatrimonioBemBaixado->inclusao();
                $arBens[] = $arTEMP['cod_bem'];
            }
            $stMsg = ( count( $arBens ) > 1 ) ? 'Bens: ' : 'Bem ';
            $stMsg.= implode( ',',$arBens );

            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,$stMsg,"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
        }

        break;

    case 'excluir' :

        $obTPatrimonioBemBaixado->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
        $obTPatrimonioBemBaixado->exclusao();

        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Bem ".$_REQUEST['inCodBem'],"excluir","aviso", Sessao::getId(), "../");

        break;

}

Sessao::encerraExcecao();
