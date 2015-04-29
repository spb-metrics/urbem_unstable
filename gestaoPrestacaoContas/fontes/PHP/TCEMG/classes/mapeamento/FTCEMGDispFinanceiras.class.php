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
    * Arquivo de mapeamento para a função que busca os dados da disp financeiras
    * Data de Criação   : 19/01/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGDispFinanceiras extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function FTCEMGDispFinanceiras()
    {
        parent::Persistente();

        $this->setTabela('tcemg.fn_disp_financeiras');

        $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
        $this->AddCampo('cod_entidade'  ,'varchar',false,''    ,false,false);
        $this->AddCampo('mes'           ,'integer',false,''    ,false,false);
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "
            SELECT mes
                 , ROUND(caixa,2) AS caixa
                 , ROUND(conta_movimento,2) AS conta_movimento
                 , ROUND(contas_vinculadas,2) AS contas_vinculadas
                 , ROUND(aplicacoes_financeiras ,2) AS aplicacoes_financeiras
                 , ROUND(compromissado,2) AS compromissado
                 , ROUND(caixa_rpps,2) AS caixa_rpps
                 , ROUND(contas_movimento_rpps,2) AS contas_movimento_rpps
                 , ROUND(contas_vinculadas_rpps,2) AS contas_vinculadas_rpps
                 , ROUND(aplicacoes_financeiras_rpps,2) AS aplicacoes_financeiras_rpps
                 , ROUND(compromissado_rpps,2) AS compromissado_rpps
                 , 'S' AS nada_declarar
                 , 0.00 AS caixa_rppsas
                 , 0.00 AS conta_movimento_rppsas
                 , 0.00 AS contas_vinculadas_rppsas
                 , 0.00 AS aplicacoes_financeiras_rppsas
                 , 0.00 AS compromissado_rppsas
                 , 0.00 AS aplicacoes_financeiras_vinc
                 , 0.00 AS aplicacoes_financeiras_vinc_rpps
                 , 0.00 AS aplicacoes_financeiras_vinc_rppsas
              FROM ".$this->getTabela()."('" . $this->getDado('exercicio') . "','" . $this->getDado('cod_entidade') . "'," . $this->getDado('mes') . ") AS retorno
                                          ( mes                         INTEGER,
                                            caixa                       DECIMAL(14,2) ,
                                            conta_movimento             DECIMAL(14,2) ,
                                            contas_vinculadas           DECIMAL(14,2) ,
                                            aplicacoes_financeiras      DECIMAL(14,2) ,
                                            compromissado               DECIMAL(14,2) ,
                                            caixa_rpps                  DECIMAL(14,2) ,
                                            contas_movimento_rpps       DECIMAL(14,2) ,
                                            contas_vinculadas_rpps      DECIMAL(14,2) ,
                                            aplicacoes_financeiras_rpps DECIMAL(14,2) ,
                                            compromissado_rpps          DECIMAL(14,2)
                                          )";
SistemaLegado::mostravar($stSql);die;
        return $stSql;
    }

}
