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
    * Classe de Visão de Relatório de Recurso Destinação
    * Data de Criação: 11/02/2009

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Fellipe Esteves dos Santos

    * @package URBEM
    * @subpackage

    * Casos de uso: UC-02.09.15
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

final class VPPARelatorioRecursoDestinacao
{

    /**
        * Método que encaminha para a Tela principal printar o Relatório de Metas
        * @param array $arParam
        * @return void
    */
    public function encaminhaRelatorioRecursoDestinacao($arParam)
    {
        $pgProg = "PRRelatorioRecursoDestinacao.php?stAcao=gerarRelatorioRecursoDestinacao";
        $pgProg.= "&inCodPPA=" . $arParam['inCodPPA'];
        $pgProg.= "&inNumPrograma=" . $arParam['inNumPrograma'];
        $pgProg.= "&inCodRecurso=" . $arParam['inCodRecurso'];

        $return = sistemaLegado::alertaAviso($pgProg, '', "incluir", "aviso", Sessao::getId(), "../");

        return $return;
    }

    /**
        * Método que executa o Relatório na Tela Principal
        * @param array $arParam
        * @return void
    */
    public function gerarRelatorioRecursoDestinacao($arParam)
    {
        # FONTE NÃO UTILIZADO, REMOVER DO REPOSITÓRIO QUANDO POSSÍVEL.
        # $preview = new PreviewBirt(2, 43, 7);
        # $preview->setTitulo('Relatório do Birt');
        # $preview->setVersaoBirt('2.2.1');
        # $preview->addParametro("cod_ppa", $arParam['inCodPPA']);
        # $preview->addParametro("num_programa", $arParam['inNumPrograma']);
        # $preview->addParametro("cod_recurso",  $arParam['inCodRecurso']);
        #
        # return $preview->preview();
    }
}

?>
