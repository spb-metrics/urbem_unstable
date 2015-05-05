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
   /*
    * Arquivo de geracao do arquivo gestaoFiscalPE TCM/MG
    * Data de Criação   : 09/03/2015

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Michel Teixeira

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: gestaoFiscalPE.inc.php 61836 2015-03-09 15:41:51Z michel $
    */

    include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGMedidas.class.php' );

    $arFiltros = Sessao::read('filtroRelatorio');

    $obTTCEMGMedidas = new TTCEMGMedidas();
    
    $stFiltro = ' WHERE medidas.cod_mes ='.$arFiltros['inPeriodo'];
    
    $obTTCEMGMedidas->recuperaDados($rsArquivo, $stFiltro);
    
    while (  !$rsArquivo->eof() ) {
        if($rsArquivo->getCampo('riscos_fiscais')=='t')
            $rsArquivo->setCampo('riscos_fiscais', 'S'); 
        else
            $rsArquivo->setCampo('riscos_fiscais', 'N');

        if($rsArquivo->getCampo('metas_fiscais')=='t')
            $rsArquivo->setCampo('metas_fiscais', 'S'); 
        else
            $rsArquivo->setCampo('metas_fiscais', 'N');

        if($rsArquivo->getCampo('contratacao_aro')=='t')
            $rsArquivo->setCampo('contratacao_aro', 'S'); 
        else
            $rsArquivo->setCampo('contratacao_aro', 'N');
        
        if($arFiltros['inPeriodo']!=12)
            $rsArquivo->setCampo('contratacao_aro', '');

        $rsArquivo->Proximo();
    }
    $rsArquivo->setPrimeiroElemento();

    $obExportador->roUltimoArquivo->addBloco($rsArquivo);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cod_mes');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('medida');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('contratacao_aro');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('riscos_fiscais');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('metas_fiscais');    
