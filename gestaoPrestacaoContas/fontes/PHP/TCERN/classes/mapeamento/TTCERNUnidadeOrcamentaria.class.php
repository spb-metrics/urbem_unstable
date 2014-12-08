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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 06/03/2012

  * @author Analista: Gelson
  * @author Desenvolvedor: Jean Felipe da Silva

*/

class TTCERNUnidadeOrcamentaria extends Persistente
{
/**
* Método Construtor
* * @access Private
*/

    public function TTCERNUnidadeOrcamentaria()
    {
        parent::Persistente();
        $this->setTabela('tcern.unidade_orcamentaria');
        $this->setCampoCod('id');

        $this->AddCampo('id',                       'integer', true,  '', true, false);
        $this->AddCampo('cod_institucional',        'numeric', true,  2, false, false);
        $this->AddCampo('cgm_unidade_orcamentaria', 'integer', true, '', false,  true);
        $this->AddCampo('cod_norma',                'integer', true, '', false,  true);
        $this->AddCampo('id_unidade_gestora',       'integer', true, '', false,  true);
        $this->AddCampo('situacao',                 'boolean', true, '', false, false);
        $this->AddCampo('exercicio',                'varchar', true,  4, false, false);
        $this->AddCampo('num_unidade',              'integer', true, '', false, false);
        $this->AddCampo('num_orgao',                 'integer', true, '', false, false);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql .= "SELECT unidade_orcamentaria.cod_institucional
                        , unidade.nom_unidade
                        , sw_cgm.numcgm
                        , sw_cgm.nom_cgm
                        , norma.cod_norma
                        , unidade.num_orgao
                        , unidade.num_unidade
                        , CASE WHEN unidade_orcamentaria.situacao = TRUE THEN 1
                             ELSE 2
                          END AS situacao
                        , unidade_orcamentaria.id AS id
                    FROM tcern.unidade_orcamentaria
                    JOIN orcamento.unidade
                      ON unidade.num_unidade = unidade_orcamentaria.num_unidade
                     AND unidade.num_orgao = unidade_orcamentaria.num_orgao
                     AND unidade.exercicio = unidade_orcamentaria.exercicio
                    JOIN sw_cgm
                      ON sw_cgm.numcgm = unidade_orcamentaria.cgm_unidade_orcamentaria
                    JOIN normas.norma
                          ON norma.cod_norma = unidade_orcamentaria.cod_norma
                    ";

        return $stSql;
    }
}
